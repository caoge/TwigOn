<?php
require_once 'vendor/autoload.php';
$loader = new Twig_Loader_Filesystem('templates');
$twig = new Twig_Environment($loader, array('debug' => true));
$twig->addExtension(new Twig_Extension_Debug());
// 扩展TAG，文章列表
class Project_Article_TokenParser extends Twig_TokenParser {
	public function parse(Twig_Token $token) {
        $parser = $this->parser;
        $stream = $parser->getStream();
        $param = array();
        

        //var_dump($parser->getStream());exit;
        
        $service = $stream->expect(Twig_Token::NAME_TYPE, 'service')->getValue();
        $stream->expect(Twig_Token::OPERATOR_TYPE, '=')->getValue();
        $service1 = $parser->getExpressionParser()->parseExpression();


        $param = $stream->expect(Twig_Token::NAME_TYPE)->getValue();
        $stream->expect(Twig_Token::OPERATOR_TYPE, '=');
        $param_value = $parser->getExpressionParser()->parseExpression();

        //$body = $this->parser->subparse(array($this, 'decideForEnd'));
        // 数据类型参数
        if ($stream->test(Twig_Token::NAME_TYPE, 'data_type')) {
            $sort = $stream->expect(Twig_Token::NAME_TYPE, 'data_type')->getValue();
            $stream->expect(Twig_Token::OPERATOR_TYPE, '=');
            $sort_value = $parser->getExpressionParser()->parseExpression();
        }
        
        $stream->expect(Twig_Token::BLOCK_END_TYPE);
        //$end = $this->parser->subparse(array($this, 'decideForEnd'), true);

        //var_dump($stream);exit;
        return new Project_Article_Node($param, $param_value, $token->getLine(), $this->getTag());
    }

    // public function decideForEnd(Twig_Token $token)
    // {
    //     return $token->test('endarticle');
    // }

    public function getTag() {
        return 'article';
    }
}

class Project_Clist_TokenParser extends Twig_TokenParser {
    public function parse(Twig_Token $token) {

    }
    public function getTag() {
        return 'Clist';
    }
}

// 转成PHP代码
class Project_Article_Node extends Twig_Node {
	public function __construct($param, Twig_Node_Expression $param_value, $line, $tag = null) {
		parent::__construct(array('param_value' => $param_value), 
			array('param' => $param), $line, $tag);
	}

	public function compile(Twig_Compiler $compiler) {
        $arr = array(
            array('id' => 1, 'name' => 'a'),
            array('id' => 2, 'name' => 'b')
        );
        $a = 1;
        $compiler
            ->addDebugInfo($this)
            ->write("\$context['_seq'] = 'dsfsf'")
            ->raw(";\n")
        ;
        //var_dump(1);exit;
        // $compiler
        //     ->write("foreach (\$context['_seq'] as ")
        //     ->subcompile($this->getNode('key_target'))
        //     ->raw(" => ")
        //     ->subcompile($this->getNode('value_target'))
        //     ->raw(") {\n")
        //     ->indent()
        //     ->subcompile($this->getNode('body'))
        //     ->outdent()
        //     ->write("}\n")
        // ;
        // $compiler
        //     ->addDebugInfo($this)
        //     ->write('$context[\''.$this->getAttribute('param').'\'] = ')
        //     ->subcompile($this->getNode('param_value'))
        //     ->raw(";\n")
        // ;
    }
}



$twig->addTokenParser(new Project_Article_TokenParser());
//$twig->addTokenParser(new Project_Set_TokenParser());

$users = array(array('id' => 1, 'name' => 'a'), array('id' => 2, 'name' => 'b'));
echo $twig->render('index.html', array('name' => 'Fabien', 'users' => $users));