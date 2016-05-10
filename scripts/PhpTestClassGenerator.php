#! /usr/bin/env php
<?php

$system_path = dirname(dirname(__FILE__));
if (realpath($system_path) !== FALSE)
{
	$system_path = realpath($system_path). DIRECTORY_SEPARATOR;
}

define('SYSTEM_PATH', str_replace("\\", DIRECTORY_SEPARATOR, $system_path));
define('APPLICATION_PATH', SYSTEM_PATH . DIRECTORY_SEPARATOR . 'application');

require SYSTEM_PATH .'application/config/bootstrap.php';
require SYSTEM_PATH .'application/config/autoload.php';
require SYSTEM_PATH .'application/config/common.php';
require SYSTEM_PATH .'application/config/errors.php';

$root = sys_get_temp_dir() . "/" . basename(__FILE__, ".php");

$config = Config::instance();
// override the logger type
$config->setValue("Repository/path", "$root" );

$config->setValue("Database/type", "sqlite" );
$config->setValue("Database/path", "db" );

$config->setValue("Logging/type", "Print") || die("Failed to change the configured Logging" . PHP_EOL);
$config->setValue("Logging/path", "logs") || die("Failed to change the configured Logging" . PHP_EOL);

$config->setValue("Repository/cache", $root . "/cache" );
$config->setValue("Repository/processing", "processing" );

$options = getopt( "f:");
if ( isset( $options['f']) == false ) {
	echo "PhpTestClassGenerator -f <path/to/Contenta.php>" . PHP_EOL;
	die( "Source file is required" . PHP_EOL );
}

$filename = $options['f'];
if ( is_file($filename) == false ) {
	echo "PhpTestClassGenerator -f <path/to/Contenta.php>" . PHP_EOL;
	die( "File '$filename' is not found" . PHP_EOL );
}

$tokens = token_get_all( file_get_contents($filename));

$stack = new Stack();
$document = new FileDocument();

$currentToken = null;
$currentClass = null;

function killme($s, $msg)
{
	echo "-=-=-=-=-=- Kill Me -=-=-=-=" . PHP_EOL;
	if ( $s != null ) {
		$s->display( "", PHP_EOL, PHP_EOL );
	}
	echo "-=-=-=-=-=- Kill Me -=-=-=-=" . PHP_EOL;
	die( $msg . PHP_EOL . PHP_EOL );
}


foreach ($tokens as $index => $tokenData) {
	if ( is_string($tokenData)) {
		if ( $tokenData == ';' ) {
			if ( $stack->peek()->isOpenContent() == false ) {
				$stack->pop();
			}
		}
		else if ( $tokenData == "=" ) {
			$stack->peek()->setHasValue(true);
		}
		else if ( $tokenData == "(" ) {
			if ( $stack->peek()->isOpenContent() == false ) {
				$stack->peek()->openArguments();
			}
		}
		else if ( $tokenData == ")" ) {
			if ( $stack->peek()->isOpenArguments() == true ) {
				$stack->peek()->closeArguments();
			}
		}
		else if ( $tokenData == "," ) {
			if ( $stack->peek()->isOpenArguments() == true ) {
				$stack->peek()->nextArgument();
			}
		}
		else if ( $tokenData == "{" ) {
// 			echo $stack->peek()->stateName()  . ' { ' . $stack->peek()->x() . PHP_EOL;
			$stack->peek()->openContent();
		}
		else if ( $tokenData == "}" ) {
// 			echo $stack->peek()->stateName() . ' } ' . $stack->peek()->x() . PHP_EOL;
			$stack->peek()->closeContent();
			if ( $stack->peek()->isOpenContent() == false ) {
// 				echo "closing " . $stack->peek() . ' } ' . $stack->peek()->x() . PHP_EOL;
				$stack->pop();
			}
		}
		else {
// 			echo $stack->peek() . ' ' . $tokenData . PHP_EOL;
		}
    }
    else if ( $stack->size() > 0
    	&& $stack->peek() instanceof FunctionToken
    	&& $stack->peek()->isOpenContent() ) {
// 		echo $stack->peek() . ' is ignoring ' . token_name($tokenData[0])
// 			. " at line " . $tokenData[2] . PHP_EOL;
    }
	else if ( is_array($tokenData) ) {
		$type = $tokenData[0];
		switch ($type) {
			case T_DOC_COMMENT:
			case T_HALT_COMPILER:
			case T_OPEN_TAG:
			case T_NS_SEPARATOR:
			case T_WHITESPACE:
			case T_RETURN:
			case T_DOUBLE_COLON:
			case T_ARRAY:
			case T_DOUBLE_ARROW:
			case T_IF:
			case T_IS_EQUAL:
			case T_SWITCH:
			case T_CLOSE_TAG:
			case T_OBJECT_OPERATOR:
			case T_CASE:
			case T_BREAK:
			case T_DEFAULT:
			case T_LNUMBER:
			case T_ISSET:
			case T_INSTANCEOF:
			case T_COMMENT:
				break;
			case T_PRIVATE:
			case T_PUBLIC:
			case T_PROTECTED:
			case T_FINAL:
			case T_STATIC:
				$currentToken = new ModifierToken($tokenData[2], $type);
				$stack->push($currentToken);
				break;
			case T_VARIABLE:
			case T_STRING:
			case T_CONSTANT_ENCAPSED_STRING:
				if ( $stack->size() == 0 ) {
					killme($stack, "Current token is null at line " . $tokenData[2]
						. " cannot append string " . $tokenData[1]);
				}
				$stack->peek()->appendValue( $tokenData[1] );
				break;
			case T_NAMESPACE:
				$currentToken = new NamespaceToken($tokenData[2], $type);
				$document->setNamespaceToken( $currentToken );
				$stack->push($currentToken);
				break;
			case T_USE:
				$currentToken = new UseObjectToken($tokenData[2], $type);
				$document->addUseToken($currentToken);
				$stack->push($currentToken);
				break;
			case T_AS:
				if ( ($stack->peek() instanceof UseObjectToken) == false ) {
					killme($stack, "Current token is " . $stack->peek()
						. " at line " . $tokenData[2]
						. " must be USE before AS" );
				}
				$stack->peek()->setState($type);
				break;
			case T_CLASS:
				$currentToken = new ClassToken($tokenData[2], $type, $type);
				$document->addClassToken( $currentToken );
				$stack->push($currentToken);
				break;
			case T_EXTENDS:
				if ( ($stack->peek() instanceof ClassToken) == false ) {
					killme($stack, "Current token is null at line " . $tokenData[2]
						. " cannot start T_EXTENDS");
				}
				$currentToken->setState($type);
				break;
			case T_CONST:
				if ( ($stack->peek() instanceof ClassToken) == false ) {
					killme($stack, "Current token is not Class at line " . $tokenData[2]
						. " cannot start T_CONST");
				}
				$currentToken = new VariableToken($tokenData[2], $type);
				$stack->peek()->appendVariable( $currentToken );
				$stack->push($currentToken);
				break;
			case T_FUNCTION:
				$currentToken = new FunctionToken($tokenData[2], $type);
				while ($stack->peek() instanceof  ModifierToken ) {
					$modifier = $stack->pop();
					$currentToken->addModifier( $modifier->stateName() );
				}

				if ( $stack->peek() instanceof ClassToken ) {
					$stack->peek()->appendFunction( $currentToken );
				}
				else {
					$document->addFunctionToken($currentToken);
				}

				$stack->push($currentToken);
				break;
			default:
				killme($stack, "Unknown token "  . $type . " (" . token_name($type)
						. ") at line " . $tokenData[2]);
				break;
		}
	}
	else {
		killme($stack, "Unknown token data "  . var_export($tokenData, true) );
	}
}

echo "-=-=-=-=-=- Stack -=-=-=-=" . PHP_EOL;
$stack->display( "", PHP_EOL, PHP_EOL );
$document->printSignatures();

class FileDocument
{
	protected $namespace = null;
	protected $useStatements = null;
	protected $functions = null;
	protected $classes = null;

    public function namespaceToken() {
		return (is_null($this->namespace) ? null : $this->namespace);
    }
    public function setNamespaceToken(NamespaceToken $line = null) {
		$this->namespace = $line;
    }

	public function useTokens() {
		return (is_null($this->useStatements) ? array() : $this->useStatements);
	}
	public function addUseToken(UseObjectToken $token = null) {
		if ( is_null($token) == false ) {
			$this->useStatements[] = $token;
		}
	}

	public function functionTokens() {
		return (is_null($this->functions) ? array() : $this->functions);
	}
	public function addFunctionToken(FunctionToken $token = null) {
		if ( is_null($token) == false ) {
			$this->functions[] = $token;
		}
	}

	public function classTokens() {
		return (is_null($this->classes) ? array() : $this->classes);
	}
	public function addClassToken(ClassToken $token = null) {
		if ( is_null($token) == false ) {
			$this->classes[] = $token;
		}
	}

	public function printSignatures() {
		echo PHP_EOL . "-=-=-=-=-=- namespace -=-=-=-=" . PHP_EOL;
		echo $this->namespaceToken() . PHP_EOL;
		echo PHP_EOL . "-=-=-=-=-=- use -=-=-=-=" . PHP_EOL;
		echo implode(PHP_EOL, $this->useTokens()) . PHP_EOL;
		echo PHP_EOL . "-=-=-=-=-=- functions -=-=-=-=" . PHP_EOL;
		echo implode(PHP_EOL, $this->functionTokens()) . PHP_EOL;
		echo PHP_EOL . "-=-=-=-=-=- classes -=-=-=-=" . PHP_EOL;
		echo implode(PHP_EOL, $this->classTokens()) . PHP_EOL;
	}
}

abstract class Token
{
    protected $line = null;
	protected $state = null;
    protected $hasValue = false;
    protected $openArguments = 0;
    protected $openContent = 0;

    public function __construct($line = -1, $type = -1) {
    	$this->setLine($line);
    	$this->setState($type);
    }

    public function line() {
		return $this->line;
    }
    public function setLine($line) {
		$this->line = $line;
		return $this;
    }

    public function state() {
		return $this->state;
    }
    public function setState($type) {
		$this->state = $type;
		return $this;
    }

    public function hasValue() {
		return $this->hasValue;
    }
    public function setHasValue($type) {
		$this->hasValue = $type;
		return $this;
    }

    public function openArguments() {
		$this->openArguments += 1;
		return $this;
    }

    public function nextArgument() {
		return $this;
    }

    public function closeArguments() {
		$this->openArguments -= 1;
		if ( $this->openArguments < 0 ) {
			throw new Exception( "ToMAny close arguments called" . $this );
		}
		return $this;
    }

    public function openContent() {
		$this->openContent += 1;
		return $this;
    }

    public function closeContent() {
		$this->openContent -= 1;
		if ( $this->openArguments < 0 ) {
			throw new Exception( "ToMAny close content called" . $this );
		}
		return $this;
    }

	public function x() { return $this->openContent; }
    public function isOpenContent() {
		return ($this->openContent > 0);
    }

    public function isOpenArguments() {
		return ($this->openArguments > 0);
    }

    public function stateName() {
    	return token_name($this->state());
    }

	public abstract function appendValue( $value );
}

class ModifierToken extends Token
{
	public function appendValue( $value ) {}
	public function __toString() {
		return $this->stateName();
	}
}

class NamespaceToken extends Token
{
    protected $namespace = null;

	public function appendValue( $value ) {
		$this->namespace .= "\\" . $value;
		return $this;
	}

	public function __toString() {
		return "namespace " . $this->namespace . ";";
	}
}

class UseObjectToken extends Token
{
    protected $fullname = null;
    protected $shortname = null;

	public function appendValue( $value ) {
		if ( $this->state() == T_AS ) {
			$this->shortname .= $value;
		}
		else {
			$this->fullname .= "\\" . $value;
		}
		return $this;
	}

	public function __toString() {
		return "use " . $this->fullname . " as " . $this->shortname . ";";
	}
}

class VariableToken extends ModifierToken
{
    protected $name = null;
    protected $value = null;
    protected $modifier = null;

    public function setState($type) {
		parent::setState($type);
		if ( $type == T_CONST ) {
			$this->modifier .= " const";
		}
		return $this;
    }

	public function appendValue( $value ) {
		if ( $this->hasValue() == true ) {
			$this->value .= $value;
		}
		else {
			$this->name .= $value;
		}
		return $this;
	}

	public function __toString() {
		return $this->stateName() . " " . $this->name . " = " . $this->value . ";";
	}
}

class ArgumentToken extends Token
{
    protected $values = null;
    protected $default = null;

	public function appendValue( $value ) {
		if ( $this->hasValue() == true ) {
			$this->default .= $value;
		}
		else {
			$this->values[] = $value;
		}
		return $this;
	}

	public function __toString() {
		$str = "";
		if ( is_null($this->values) == false ) {
			$str = implode(" ", $this->values);
			if ( is_null($this->default) == false ) {
				$str .= " = " . $this->default;
			}
		}
		return $str;
	}
}

class FunctionToken extends Token
{
    protected $name = null;
    protected $arguments = null;
    protected $modifier = null;
    protected $currentArg = null;

	public function appendValue( $value ) {
		if ( $this->isOpenContent() ) {
			// ignore
		}
		else if ( $this->isOpenArguments() ) {
			if ( is_null( $this->currentArg ) ) {
				$this->currentArg = new ArgumentToken();
				$this->arguments[] = $this->currentArg;
			}
			$this->currentArg->appendValue( $value );
		}
		else {
			$this->name .= $value;
		}

		return $this;
	}

    public function setHasValue($type) {
		if ( is_null( $this->currentArg ) == false ) {
			$this->currentArg->setHasValue( $type);
		}
		return parent::setHasValue( $type );
    }

    public function nextArgument() {
    	$this->currentArg = null;
		return parent::nextArgument( );
    }

    public function modifier() {
		return $this->modifier;
    }
    public function addModifier($type) {
		$this->modifier[] = $type;
		return $this;
    }

	public function __toString() {

		return $this->stateName() . " "
			. (is_null($this->modifier) ? "" : implode(" ", $this->modifier) . " " )
			. $this->name . " ( "
			. (is_null($this->arguments) ? "" : implode(", ", $this->arguments))
			. ")";
	}
}

class ClassToken extends Token
{
    protected $name = null;
    protected $extends = null;
    protected $intefaces = null;

	protected $variables = null;
	protected $functions = null;

	public function appendVariable( VariableToken $variable ) {
		$this->variables[] = $variable;
		return $this;
	}

	public function appendFunction( FunctionToken $funct ) {
		$this->functions[] = $funct;
		return $this;
	}

	public function appendValue( $value ) {
		switch ( $this->state() ) {
			case T_CLASS:
				$this->name .= $value;
				break;
			case T_EXTENDS:
				$this->extends .= $value;
				break;
			default:
				die( "Unknown state for class "  . $this
					. " (" . $this->stateName() . ")" . PHP_EOL);
				break;
		}
		return $this;
	}

	public function __toString() {
		$str = "T_CLASS " . $this->name . " extends  " . $this->extends . PHP_EOL;
		if ( is_array($this->intefaces) && count($this->intefaces) > 0) {
			$str .= "	implements " . implode(", ", $this->intefaces) . PHP_EOL;
		}
		if ( is_array($this->variables) && count($this->variables) > 0) {
			$str .= "\t" . implode("\n\t", $this->variables) . PHP_EOL;
		}
		if ( is_array($this->functions) && count($this->functions) > 0) {
			$str .= "\t" . implode("\n\t", $this->functions) . PHP_EOL;
		}
		return $str;
	}
}


class Stack
{
    protected $vals;
    protected $amount;
    protected $maximum;

    //constructor
    function Stack() {
        $this->amount=0;
        $this->maximum=0;
    }

    function push($number){
        if ($this->maximum > $this->amount){
            $this->vals[$this->amount]=$number;
        } else {
            $this->vals[]=$number;
            $this->maximum++;
        };
        $this->amount++;
        return $this->amount;
    }

    function pop(){
        if ($this->amount){
            $this->amount--;
            return $this->vals[$this->amount];
        } else {
            die("stack empty" . PHP_EOL);
        };
    }

    function peek(){
        if ($this->amount){
            return $this->vals[($this->amount - 1)];
        } else {
            die("stack empty" . PHP_EOL);
        };
    }

    function display($before,$after,$ending){
        for ($i=0; $i < $this->amount; $i++) {
            echo $before.$this->vals[$i].$after;
        };
        echo $ending;
    }

    function size(){
        return $this->amount;
    }

    function maxsize(){
        return $this->maximum;
    }

};
