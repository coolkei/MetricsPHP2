<?php
require_once __DIR__.'/../vendor/autoload.php';

/**
 * Class MyVisitor
 */
class MyVisitor extends \PhpParser\NodeVisitorAbstract {

    /**
     * @var
     */
    private $callback;

    /**
     * MyVisitor constructor.
     * @param $callback
     */
    public function __construct($callback)
    {
        $this->callback = $callback;
    }

    /**
     * @inheritdoc
     */
    public function leaveNode(\PhpParser\Node $node) {
        call_user_func($this->callback, $node);
    }
}

/**
 * @param $node
 * @param $callback
 */
function iterate_over_node($node, $callback) {

    $myVisitor = new MyVisitor($callback);
    $traverser = new \PhpParser\NodeTraverser();
    $traverser->addVisitor($myVisitor);
    $traverser->traverse(array($node));
    return;
}

/**
 * @param $node
 * @return string
 */
function getNameOfNode($node) {
    if(is_string($node)) {
        return $node;
    }

    if($node instanceof \PhpParser\Node\Name\FullyQualified) {
        return (string) $node;
    }
    if($node instanceof \PhpParser\Node\Expr\New_) {
        return getNameOfNode($node->class);
    }

    if(isset($node->class)) {
        return getNameOfNode($node->class);
    }

    if($node instanceof \PhpParser\Node\Name) {
        return (string) implode($node->parts);
    }

    if($node->name instanceof \PhpParser\Node\Expr\Variable) {

        return (string) $node->name->name;
    }

    if($node->name instanceof \PhpParser\Node\Expr\MethodCall) {
        return getNameOfNode($node->name);
    }

    if($node instanceof \PhpParser\Node\Expr\ArrayDimFetch) {
        return getNameOfNode($node->var);
    }

    if($node->name instanceof \PhpParser\Node\Expr\BinaryOp) {
        return get_class($node->name);
    }

    return (string) $node->name;
}

/**
 * @param $src
 * @param $dst
 */
function recurse_copy($src,$dst) {
    $dir = opendir($src);
    @mkdir($dst);
    while(false !== ( $file = readdir($dir)) ) {
        if (( $file != '.' ) && ( $file != '..' )) {
            if ( is_dir($src . '/' . $file) ) {
                recurse_copy($src . '/' . $file,$dst . '/' . $file);
            }
            else {
                copy($src . '/' . $file,$dst . '/' . $file);
            }
        }
    }
    closedir($dir);
}