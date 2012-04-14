<?php

/*
 * (C)2001-2011 Comsenz Inc.
 * This is NOT a freeware, use is subject to license terms
 * GavinYao <Yaojungnag@comsenz.com>
 */

/**
 * graph
 *
 * @author y109
 */
class Tools_Model_GraphvizGraph extends Etao_Model_Base
{

    protected $_name = 'graphviz_graph';
    protected $_primary = 'id';
    protected $_fields = array(
        'id',
        'name',
        'label',
        'attrs'
    );
    private $nodeObj;
    private $edgeObj;
    private $graphvizObj;
    private $isBuilded = false;

    public function __construct()
    {
        parent::__construct();
        $this->nodeObj = new Tools_Model_GraphvizNode();
        $this->edgeObj = new Tools_Model_GraphvizEdge;
        $this->graphvizObj = new Etao_Image_GraphViz();
    }

    /**
     * 添加
     * @param type $data
     * @return type
     */
    public function addGraphvizGraph($data)
    {
        return $this->add($data);
    }

    /**
     * 修改
     * @param type $data
     * @return type
     */
    public function updateGraphvizGraph($data)
    {
        return $this->update($data, 'id = "' . $data['id'] . '"');
    }

    /**
     * 删除
     * @param type $id
     * @return type
     */
    public function deleteGraphvizGraph($id)
    {
        $this->nodeObj->deleteByGid($id);
        $this->edgeObj->deleteByGid($id);
        return $this->delByID($id);
    }

    /**
     * 根据名称获取对象
     * @param type $id
     * @return type
     */
    public function getById($id)
    {
        return $this->findById($id);
    }

    /**
     * 根据名称查询对象
     * @param type $name
     * @return type
     */
    public function findByName($name)
    {
        return $this->queryByFieldAndValue('name', $name);
    }

    /**
     * 拼接图像
     * @param type $id
     */
    public function findGraphById($id)
    {
        $graph = $this->findById($id);
        if($graph) {
            $graph = $graph->toArray();
            $graph['nodes'] = $this->nodeObj->findByGid($id);
            $graph['edges'] = $this->edgeObj->findByGid($id);
            return (object)$graph;
        }
        return null;
    }

    private function buildGraphviz($id)
    {
        if($this->isBuilded) {
            return;
        } else {
            $graph = $this->findGraphById($id);
            if($graph) {
                $gv = $this->graphvizObj;
                //添加节点
                foreach($graph->nodes as $node)
                {
                    $attr = array();
                    strlen($node->label) > 0 && $attr['label'] = $node->label;
                    //节点扩展属性
                    $nAttrs = json_decode($node->attrs);
                    if($nAttrs) {
                        foreach($nAttrs as $k => $v)
                        {
                            strlen($v) > 0 && $attr[$k] = $v;
                        }
                    }
                    $gv->addNode($node->name, $attr);
                    unset($attr);
                }
                //添加边
                foreach($graph->edges as $edge)
                {
                    $attr = array();
                    strlen($edge->label) > 0 && $attr['label'] = $edge->label;
                    //边扩展属性
                    $eAttrs = json_decode($edge->attrs);
                    if($eAttrs) {
                        foreach($eAttrs as $k => $v)
                        {
                            strlen($v) > 0 && $attr[$k] = $v;
                        }
                    }
                    //直接生成
                    //$gv->addEdge(array(trim($edge->node1) => trim($edge->node2)), $attr);
                    // 展开节点
                    switch((int)$edge->type)
                    {
                        case Tools_Model_GraphvizEdge::TYPE_ONE2ONE:
                            $gv->addEdge(array(trim($edge->node1) => trim($edge->node2)), $attr);
                            break;
                        case Tools_Model_GraphvizEdge::TYPE_ONE2MANY:
                            $nodes = explode(',', $edge->node2);
                            foreach($nodes as $value)
                            {
                                $gv->addEdge(array(trim($edge->node1) => trim($value)), $attr);
                            }
                            break;
                        case Tools_Model_GraphvizEdge::TYPE_LINK:
                            $nodes = explode(',', $edge->node2);
                            $l = count($nodes);
                            $node_last = trim($edge->node1);
                            for($i = 0; $i < $l; $i++)
                            {
                                $gv->addEdge(array($node_last => trim($nodes[$i])), $attr);
                                $node_last = trim($nodes[$i]);
                            }
                            break;
                        default:
                            break;
                    }
                    unset($attr);
                }
            }
            //label
            strlen($graph->label) > 0 && $gv->addAttributes(array('label' => $graph->label));
            //图扩展属性
            $gAttrs = json_decode($graph->attrs);
            if($gAttrs) {
                foreach($gAttrs as $k => $v)
                {
                    strlen($v) > 0 && $gv->addAttributes(array($k => $v));
                }
            }
            //引擎
            $gv->setLayoutEngine($graph->type);
            //文件格式
            $gv->setFileFormat($graph->file_format);
            //有向图
            $gv->setDirected((int)$graph->directed == 1);
            //严格模式
            $gv->setStrict((int)$graph->strict == 1);
            $this->isBuilded = true;
        }
    }

    /**
     * 生成 graphviz 代码
     * @param type $id
     */
    public function getCode($id)
    {
        $graph = $this->findById($id);
        if($graph) {
            if((int)$graph->advanced == 1) {
                return $graph->code;
            } else {
                $this->buildGraphviz($id);
                $code = $this->graphvizObj->parse();
                $this->updateGraphvizGraph(array('id' => $id, 'code' => $code));
                return $code;
            }
        }
    }

    /**
     * 生成图像
     * @param type $id
     */
    public function getImage($id)
    {
        $graph = $this->findById($id);
        if($graph) {
            if((int)$graph->advanced == 1) {
                //引擎
                $this->graphvizObj->setLayoutEngine($graph->type);
                //文件格式
                $this->graphvizObj->setFileFormat($graph->file_format);
                $this->graphvizObj->imageFromCode($graph->code);
            } else {
                $this->buildGraphviz($id);
                $this->graphvizObj->image();
            }
        }
    }

}