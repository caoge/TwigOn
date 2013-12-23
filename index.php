<?php
require_once 'vendor/autoload.php';
$loader = new Twig_Loader_Filesystem('templates');
$twig = new Twig_Environment($loader, array('debug' => true));
$twig->addExtension(new Twig_Extension_Debug());
// 扩展TAG，文章列表
class Project_Article_TokenParser extends Twig_TokenParser {
	public function parse(Twig_Token $token) {
        $lineno = $token->getLine();
        $parser = $this->parser;
        $stream = $parser->getStream();
        //$targets = $parser->getExpressionParser()->parseAssignmentExpression();
        //var_dump($targets);exit;
        $param = array();
        
        //var_dump($stream);
        //var_dump($parser->getStream());exit;
        
        $service = $stream->expect(Twig_Token::NAME_TYPE, 'service')->getValue();
        $b = $stream->expect(Twig_Token::OPERATOR_TYPE, '=')->getValue();
        $service_name = $parser->getExpressionParser()->parseExpression();


        $param = $stream->expect(Twig_Token::NAME_TYPE)->getValue();
        
        $stream->expect(Twig_Token::OPERATOR_TYPE, '=');
        //$test = $stream->expect(Twig_Token::STRING_TYPE);

        $param_value = $parser->getExpressionParser()->parseExpression();
        // if ($param_token = $stream->nextIf(Twig_Token::STRING_TYPE)) {
        //     $param_value = $param_token->getValue();
        // }

        //var_dump($param_value);
        // $param_value = $stream->nextIf(Twig_Token::STRING_TYPE)->getValue();
       // var_dump(json_encode(array('a' => 333, 'b' => 'sfsf')));//[333,"sfsf"]
        //var_dump(json_decode('{"user_id":"3", "user_name":"caoge"}'));exit;

        // while (!$token->test(Twig_Token::BLOCK_END_TYPE, 'endarticle')) {
        //     var_dump(2);
        // }


        //$body = $this->parser->subparse(array($this, 'decideForEnd'));
        // 数据类型参数
        if ($stream->test(Twig_Token::NAME_TYPE, 'data_type')) {
            $sort = $stream->expect(Twig_Token::NAME_TYPE, 'data_type')->getValue();
            $stream->expect(Twig_Token::OPERATOR_TYPE, '=');
            $sort_value = $parser->getExpressionParser()->parseExpression();
        }

        $stream->expect(Twig_Token::BLOCK_END_TYPE);
        $body = $parser->subparse(array($this, 'decideArticleEnd'), true);
        $stream->expect(Twig_Token::BLOCK_END_TYPE);

        
        $keyTarget = new Twig_Node_Expression_AssignName('_key', $lineno);
        $valueTarget = new Twig_Node_Expression_AssignName('_field', $lineno);


        //$aaa = new Twig_Node_Expression_AssignName('aaa', $lineno);

        //var_dump($keyTarget);
        //var_dump($stream->expect(Twig_Token::NAME_TYPE)->getValue());exit;
        // $body = $parser->subparse(array($this, 'decideArticleEnd'));
        // if ($stream->next()->getValue() == 'endarticle') {

        //     //var_dump($stream->expect(Twig_Token::BLOCK_END_TYPE));exit;
        //     $stream->expect(Twig_Token::BLOCK_END_TYPE);

        //     $else = $parser->subparse(array($this, 'decideArticleEnd'), true);
        //     //var_dump($else);exit;
        // } else {
        //     $else = null;
        // }
       //var_dump($token->test('endarticle'));exit;

        //$stream->expect(Twig_Token::BLOCK_END_TYPE);
        //$end = $this->parser->subparse(array($this, 'decideForEnd'), true);

        //var_dump($stream);exit;

        return new Project_Article_Node($keyTarget, $valueTarget, $body, $param, $param_value, $token->getLine(), $this->getTag());
    }

    // public function decideForEnd(Twig_Token $token)
    // {
    //     return $token->test('endarticle');
    // }

    public function decideArticleEnd(Twig_Token $token){
        return $token->test('endarticle');
    }

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
	public function __construct(Twig_Node_Expression_AssignName $keyTarget, Twig_Node_Expression_AssignName $valueTarget, Twig_NodeInterface $body, $param, Twig_Node_Expression $param_value, $lineno, $tag = null) {
        $body = new Twig_Node(array($body, $this->loop = new Twig_Node_ForLoop($lineno, $tag)));//Twig_Node_Expression param_value
        //var_dump($param);
        parent::__construct(array('key_target' => $keyTarget, 'value_target' => $valueTarget, 'body' => $body, 'param_value' => $param_value), array('with_loop' => true, 'param' => $param), $lineno, $tag);

		//parent::__construct(array('param_value' => $param_value), array('param' => $param), $lineno, $tag);
	}

	public function compile(Twig_Compiler $compiler) {
        $arr = array(
            array('id' => 1, 'name' => 'a'),
            array('id' => 2, 'name' => 'b')
        );
        $a = json_encode(array('t' => array('caoge', 'bbb'), 'b' => array('ccc', 'ddd')));
        //var_dump($a);
        //var_dump(json_decode('["caoge","bbb"]'));
        $compiler
            ->addDebugInfo($this)
            ->write("\$context['_seq'] = array(11);")
            ->write("\$context['json'] = json_decode('".$a."', true);")
            ->raw(";\n")
        ;
        //var_dump($aaa);exit;
        //getKeyValuePairs
        //$aaa = $this->getNode('param_value')->getKeyValuePairs();
        //var_dump($aaa);exit;
        //var_dump($aaa[0]['key']);exit;

        $compiler
            ->addDebugInfo($this)
            ->write('$context[\''.$this->getAttribute('param').'\'] = ')
            ->subcompile($this->getNode('param_value'))
            ->raw(";\n")
        ;

        
        $compiler
            ->write("foreach (\$context['json'] as ")
            ->subcompile($this->getNode('key_target'))
            ->raw(" => ")
            ->subcompile($this->getNode('value_target'))
            ->raw(") {\n")
            ->indent()
            ->subcompile($this->getNode('body'))
            ->outdent()
            ->write("}\n")
        ;
        error_log($compiler->getSource(), 3, "log.php");
    }
}

$twig->addTokenParser(new Project_Article_TokenParser());
//$twig->addTokenParser(new Project_Set_TokenParser());

$users = array(array('id' => 1, 'name' => 'a'), array('id' => 2, 'name' => 'b'));
echo $twig->render('index.html', array('name' => 'Fabien', 'users' => $users));