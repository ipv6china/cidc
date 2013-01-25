<?php

/**
 * Base On Pear Image_GraphViz
 *
 */
class Etao_Image_GraphViz
{

    /**
     * Base path to GraphViz commands
     *
     * @var string
     */
    var $binPath = '/usr/local/bin/';

    /**
     * Path to GraphViz/dot command
     *
     * @var string
     */
    var $dotCommand = 'dot';

    /**
     * Path to GraphViz/neato command
     *
     * @var string
     */
    var $neatoCommand = 'neato';

    /**
     * Default layout engion
     * @var string
     */
    var $layoutEngine = 'dot';
    private $_avilableLayoutEngine = array('dot', 'neato', 'twopi', 'circo', 'fdp');
    private $fileFormat = 'svg';
    private $strict = FALSE;
    private $directed = TRUE;

    /**
     * Representation of the graph
     *
     * @var array
     */
    var $graph = array('edgesFrom' => array(),
        'nodes' => array(),
        'attributes' => array(),
        'clusters' => array(),
        'subgraphs' => array(),
        'name' => 'G'
    );

    /**
     * Whether to return self_Error instance on failures instead of FALSE
     *
     * @var boolean
     * @access protected
     */
    var $_returnFalseOnError = true;

    /**
     * Constructor.
     *
     * Setting the name of the Graph is useful for including multiple image
     * maps on one page. If not set, the graph will be named 'G'.
     *
     * @param array   $attributes  Attributes of the graph
     * @param string  $name        Name of the Graph
     * @param boolean $returnError Set to TRUE to return self_Error instances
     *                             on failures instead of FALSE
     *
     * @access public
     */
    function Etao_Image_GraphViz($attributes = array(), $name = 'G', $returnError = false)
    {
        //set binPath
        if(substr(PHP_OS, 0, 3) === 'WIN') {
            $this->setBinPath('');
        } else {
            $this->setBinPath('/usr/local/bin/');
        }
        if(defined('CORE_PATH') && file_exists(CORE_PATH . 'config.php')) {
            $config = include CORE_PATH . 'config.php';
            if(is_array($config) && isset($config['dotPath'])) {
                $this->setBinPath($config['dotPath']);
            }
        }
        $this->setAttributes($attributes);
        $this->graph['name'] = $name;

        $this->_returnFalseOnError = !$returnError;
    }

    public function getBinPath()
    {
        return $this->binPath;
    }

    public function setBinPath($binPath)
    {
        $this->binPath = $binPath;
    }

    public function getDirected()
    {
        return $this->directed;
    }

    public function setDirected($directed)
    {
        $this->directed = $directed;
    }

    public function getFileFormat()
    {
        return $this->fileFormat;
    }

    public function setFileFormat($fileFormat)
    {
        $this->fileFormat = $fileFormat;
    }

    public function getStrict()
    {
        return $this->strict;
    }

    public function setStrict($strict)
    {
        $this->strict = $strict;
    }

    public function getLayoutEngine()
    {
        return $this->layoutEngine;
    }

    public function setLayoutEngine($layoutEngine)
    {
        if(in_array($layoutEngine, $this->_avilableLayoutEngine)) {
            $this->layoutEngine = $layoutEngine;
        } else {
            $this->layoutEngine = $this->_avilableLayoutEngine[0];
        }
    }

    /**
     * 从 Graphviz Code 生成图像
     * @param type $code
     * @return type
     */
    public function imageFromCode($code)
    {
        $filename = tempnam(sys_get_temp_dir(), 'CIDC_');
        file_put_contents($filename, $code);
        return $this->image($filename);
    }

    /**
     * Outputs image of the graph in a given format
     *
     * This methods send HTTP headers
     *
     * @param string $format  Format of the output image. This may be one
     *                        of the formats supported by GraphViz.
     * @param string $command "dot" or "neato"
     *
     * @return boolean TRUE on success, FALSE or self_Error otherwise
     * @access public
     */
    public function image($code_file='')
    {
        $format = $this->getFileFormat();
        $command = $this->getLayoutEngine();
        if($code_file) {
            $file = $code_file;
        } else {
            $file = $this->saveParsedGraph();
        }
        if(!$file || self::isError($file)) {
            return $file;
        }

        $outputfile = $file . '.' . $format;

        $rendered = $this->renderDotFile($file, $outputfile, $command);
        if($rendered !== true) {
            return $rendered;
        }

        $sendContentLengthHeader = true;

        switch(strtolower($format))
        {
            case 'gif':
            case 'png':
            case 'bmp':
            case 'jpeg':
            case 'tiff':
                header('Content-Type: image/' . $format);
                break;

            case 'tif':
                header('Content-Type: image/tiff');
                break;

            case 'jpg':
                header('Content-Type: image/jpeg');
                break;

            case 'ico':
                header('Content-Type: image/x-icon');
                break;

            case 'wbmp':
                header('Content-Type: image/vnd.wap.wbmp');
                break;

            case 'pdf':
                header('Content-Type: application/pdf');
                break;

            case 'mif':
                header('Content-Type: application/vnd.mif');
                break;

            case 'vrml':
                header('Content-Type: application/x-vrml');
                break;

            case 'svg':
                header('Content-Type: image/svg+xml');
                break;

            case 'plain':
            case 'plain-ext':
                header('Content-Type: text/plain');
                break;

            default:
                header('Content-Type: application/octet-stream');
                $sendContentLengthHeader = false;
        }

        if($sendContentLengthHeader) {
            header('Content-Length: ' . filesize($outputfile));
        }

        $return = true;
        if(readfile($outputfile) === false) {
            $return = false;
        }
        @unlink($file);
        @unlink($outputfile);

        return $return;
    }

    /**
     * Returns image (data) of the graph in a given format.
     *
     * @param string $command "dot" or "neato"
     *
     * @return string The image (data) created by GraphViz, FALSE or self_Error
     *                on error
     * @access public
     * @since  Method available since Release 1.1.0
     */
    function fetch($command = null)
    {
        $format = $this->getFileFormat();
        $file = $this->saveParsedGraph();
        if(!$file || self::isError($file)) {
            return $file;
        }

        $outputfile = $file . '.' . $format;

        $rendered = $this->renderDotFile($file, $outputfile, $command);
        if($rendered !== true) {
            return $rendered;
        }

        @unlink($file);

        $fp = fopen($outputfile, 'rb');

        if(!$fp) {
            if($this->_returnFalseOnError) {
                return false;
            }
            $error = new Exception('Could not read rendered file');
            return $error;
        }

        $data = fread($fp, filesize($outputfile));
        fclose($fp);
        @unlink($outputfile);

        return $data;
    }

    /**
     * Renders a given dot file into a given format.
     *
     * @param string $dotfile    The absolute path of the dot file to use.
     * @param string $outputfile The absolute path of the file to save to.
     * @param string $command    "dot" or "neato"
     *
     * @return boolean TRUE if the file was saved, FALSE or self_Error
     *                 otherwise.
     * @access public
     */
    function renderDotFile($dotfile, $outputfile, $command = null)
    {
        $format = $this->getFileFormat();
        if(!file_exists($dotfile)) {
            if($this->_returnFalseOnError) {
                return false;
            }
            $error = new Exception('Could not find dot file');
            return $error;
        }
        $oldmtime = file_exists($outputfile) ? filemtime($outputfile) : 0;

        switch($command)
        {
            case 'dot':
            case 'neato':
                break;
            default:
                $command = $this->getDirected() ? 'dot' : 'neato';
        }
        $command_orig = $command;

        $command = $this->binPath . (($command == 'dot') ? $this->dotCommand : $this->neatoCommand);

        $command .= ' -T' . escapeshellarg($format)
                . ' -K' . escapeshellarg($this->getLayoutEngine())
                . ' -o' . escapeshellarg($outputfile)
                . ' ' . escapeshellarg($dotfile)
                . ' 2>&1';
        exec($command, $msg, $return_val);
        clearstatcache();
        if(file_exists($outputfile) && filemtime($outputfile) > $oldmtime
                && $return_val == 0) {
            return true;
        } elseif($this->_returnFalseOnError) {
            return false;
        }
        $error = new Exception($command_orig . ' command failed: '
                        . implode("\n", $msg));
        return $error;
    }

    /**
     * Adds a cluster to the graph.
     *
     * A cluster is a subgraph with a rectangle around it.
     *
     * @param string $id         ID.
     * @param array  $title      Title.
     * @param array  $attributes Attributes of the cluster.
     * @param string $group      ID of group to nest cluster into
     *
     * @return void
     * @access public
     * @see    addSubgraph()
     */
    function addCluster($id, $title, $attributes = array(), $group = 'default')
    {
        $this->graph['clusters'][$id]['title'] = $title;
        $this->graph['clusters'][$id]['attributes'] = $attributes;
        $this->graph['clusters'][$id]['embedIn'] = $group;
    }

    /**
     * Adds a subgraph to the graph.
     *
     * @param string $id         ID.
     * @param array  $title      Title.
     * @param array  $attributes Attributes of the cluster.
     * @param string $group      ID of group to nest subgraph into
     *
     * @return void
     * @access public
     */
    function addSubgraph($id, $title, $attributes = array(), $group = 'default')
    {
        $this->graph['subgraphs'][$id]['title'] = $title;
        $this->graph['subgraphs'][$id]['attributes'] = $attributes;
        $this->graph['subgraphs'][$id]['embedIn'] = $group;
    }

    /**
     * Adds a note to the graph.
     *
     * @param string $name       Name of the node.
     * @param array  $attributes Attributes of the node.
     * @param string $group      Group of the node.
     *
     * @return void
     * @access public
     */
    function addNode($name, $attributes = array(), $group = 'default')
    {
        $this->graph['nodes'][$group][$name] = $attributes;
    }

    /**
     * Removes a node from the graph.
     *
     * This method doesn't remove edges associated with the node.
     *
     * @param string $name  Name of the node to be removed.
     * @param string $group Group of the node.
     *
     * @return void
     * @access public
     */
    function removeNode($name, $group = 'default')
    {
        if(isset($this->graph['nodes'][$group][$name])) {
            unset($this->graph['nodes'][$group][$name]);
        }
    }

    /**
     * Adds an edge to the graph.
     *
     * Examples:
     * <code>
     * $g->addEdge(array('node1' => 'node2'));
     * $attr = array(
     *     'label' => '+1',
     *     'style' => 'dashed',
     * );
     * $g->addEdge(array('node3' => 'node4'), $attr);
     *
     * // With port specification
     * $g->addEdge(array('node5' => 'node6'), $attr, array('node6' => 'portA'));
     * $g->addEdge(array('node7' => 'node8'), null, array('node7' => 'portC',
     *                                                    'node8' => 'portD'));
     * </code>
     *
     * @param array $edge       Start => End node of the edge.
     * @param array $attributes Attributes of the edge.
     * @param array $ports      Start node => port, End node => port
     *
     * @return integer an edge ID that can be used with {@link removeEdge()}
     * @access public
     */
    function addEdge($edge, $attributes = array(), $ports = array())
    {
        if(!is_array($edge)) {
            return;
        }
        $from = key($edge);
        $to = $edge[$from];
        $info = array();

        if(is_array($ports)) {
            if(array_key_exists($from, $ports)) {
                $info['portFrom'] = $ports[$from];
            }

            if(array_key_exists($to, $ports)) {
                $info['portTo'] = $ports[$to];
            }
        }

        if(is_array($attributes)) {
            $info['attributes'] = $attributes;
        }

        if($this->getStrict()) {
            if(!isset($this->graph['edgesFrom'][$from][$to][0])) {
                $this->graph['edgesFrom'][$from][$to][0] = $info;
            } else {
                $this->graph['edgesFrom'][$from][$to][0] = array_merge($this->graph['edgesFrom'][$from][$to][0], $info);
            }
        } else {
            $this->graph['edgesFrom'][$from][$to][] = $info;
        }

        return count($this->graph['edgesFrom'][$from][$to]) - 1;
    }

    /**
     * Removes an edge from the graph.
     *
     * @param array   $edge Start and End node of the edge to be removed.
     * @param integer $id   specific edge ID (only usefull when multiple edges
     *                      exist between the same 2 nodes)
     *
     * @return void
     * @access public
     */
    function removeEdge($edge, $id = null)
    {
        if(!is_array($edge)) {
            return;
        }

        $from = key($edge);
        $to = $edge[$from];

        if(!is_null($id)) {
            if(isset($this->graph['edgesFrom'][$from][$to][$id])) {
                unset($this->graph['edgesFrom'][$from][$to][$id]);

                if(count($this->graph['edgesFrom'][$from][$to]) == 0) {
                    unset($this->graph['edgesFrom'][$from][$to]);
                }
            }
        } elseif(isset($this->graph['edgesFrom'][$from][$to])) {
            unset($this->graph['edgesFrom'][$from][$to]);
        }
    }

    /**
     * Adds attributes to the graph.
     *
     * @param array $attributes Attributes to be added to the graph.
     *
     * @return void
     * @access public
     */
    function addAttributes($attributes)
    {
        if(is_array($attributes)) {
            $this->graph['attributes'] = array_merge($this->graph['attributes'], $attributes);
        }
    }

    /**
     * Sets attributes of the graph.
     *
     * @param array $attributes Attributes to be set for the graph.
     *
     * @return void
     * @access public
     */
    function setAttributes($attributes)
    {
        if(is_array($attributes)) {
            $this->graph['attributes'] = $attributes;
        }
    }

    /**
     * Escapes an (attribute) array
     *
     * Detects if an attribute is <html>, contains double-quotes, etc...
     *
     * @param array $input input to escape
     *
     * @return array input escaped
     * @access protected
     */
    function _escapeArray($input)
    {
        $output = array();

        foreach((array)$input as $k => $v)
        {
            switch($k)
            {
                case 'label':
                case 'headlabel':
                case 'taillabel':
                    $v = $this->_escape($v, true);
                    break;
                default:
                    $v = $this->_escape($v);
                    $k = $this->_escape($k);
            }

            $output[$k] = $v;
        }

        return $output;
    }

    /**
     * Returns a safe "ID" in DOT syntax
     *
     * @param string  $input string to use as "ID"
     * @param boolean $html  whether to attempt detecting HTML-like content
     *
     * @return string
     * @access protected
     */
    function _escape($input, $html = false)
    {
        switch(strtolower($input))
        {
            case 'node':
            case 'edge':
            case 'graph':
            case 'digraph':
            case 'subgraph':
            case 'strict':
                return '"' . $input . '"';
        }

        if(is_bool($input)) {
            return ($input) ? 'true' : 'false';
        }

        if($html && (strpos($input, '</') !== false
                || strpos($input, '/>') !== false)) {
            return '<' . $input . '>';
        }

        if(preg_match('/^([a-z_][a-z_0-9]*|-?(\.[0-9]+|[0-9]+(\.[0-9]*)?))$/i', $input)) {
            return $input;
        }

        return '"' . str_replace(array("\r\n", "\n", "\r", '"'), array('\n', '\n', '\n', '\"'), $input) . '"';
    }

    /**
     * Loads a graph from a file in Image_GraphViz format
     *
     * @param string $file File to load graph from.
     *
     * @return void
     * @access public
     */
    function load($file)
    {
        if($serializedGraph = implode('', @file($file))) {
            $g = unserialize($serializedGraph);

            if(!is_array($g)) {
                return;
            }

            // Convert old storage format to new one
            $defaults = array('edgesFrom' => array(),
                'nodes' => array(),
                'attributes' => array(),
                'clusters' => array(),
                'subgraphs' => array(),
                'name' => 'G'
            );

            $this->graph = array_merge($defaults, $g);

            if(isset($this->graph['edges'])) {
                foreach($this->graph['edges'] as $id => $nodes)
                {
                    $attr = (isset($this->graph['edgeAttributes'][$id])) ? $this->graph['edgeAttributes'][$id] : array();

                    $this->addEdge($nodes, $attr);
                }

                unset($this->graph['edges']);
                unset($this->graph['edgeAttributes']);
            }
        }
    }

    /**
     * Save graph to file in Image_GraphViz format
     *
     * This saves the serialized version of the instance, not the
     * rendered graph.
     *
     * @param string $file File to save the graph to.
     *
     * @return string File the graph was saved to, FALSE or self_Error on
     *                failure.
     * @access public
     */
    function save($file = '')
    {
        $serializedGraph = serialize($this->graph);

        if(empty($file)) {
            $file = tempnam(sys_get_temp_dir(), 'CIDC_');
        }

        file_put_contents($file, $serializedGraph);
        return $file;

        if($this->_returnFalseOnError) {
            return false;
        }
        $error = new Exception('Could not save serialized graph instance');
        return $error;
    }

    /**
     * Returns a list of sub-groups for a given parent group
     *
     * @param string $parent Group ID
     *
     * @return array list of group IDs
     * @access protected
     */
    function _getSubgraphs($parent)
    {
        $subgraphs = array();
        foreach($this->graph['clusters'] as $id => $info)
        {
            if($info['embedIn'] === $parent) {
                $subgraphs[] = $id;
            }
        }
        foreach($this->graph['subgraphs'] as $id => $info)
        {
            if($info['embedIn'] === $parent) {
                $subgraphs[] = $id;
            }
        }
        return $subgraphs;
    }

    /**
     * Returns a list of cluster/subgraph IDs
     *
     * @return array
     * @access protected
     */
    function _getGroups()
    {
        $groups = array_merge(array_keys($this->graph['clusters']), array_keys($this->graph['subgraphs']));
        return array_unique($groups);
    }

    /**
     * Returns a list of top groups
     *
     * @return array
     * @access protected
     */
    function _getTopGraphs()
    {
        $top = array();
        $groups = $this->_getGroups();

        foreach($groups as $id)
        {
            $isTop = ($id === 'default');
            if(isset($this->graph['clusters'][$id])
                    && $this->graph['clusters'][$id]['embedIn'] === 'default') {
                $isTop = true;
            }
            if(isset($this->graph['subgraphs'][$id])
                    && $this->graph['subgraphs'][$id]['embedIn'] === 'default') {
                $isTop = true;
            }
            if($isTop) {
                $top[] = $id;
            }
        }

        return array_unique($top);
    }

    /**
     * Parses the graph into GraphViz markup.
     *
     * @return string GraphViz markup
     * @access public
     */
    function parse()
    {
        $parsedGraph = $this->getStrict() ? 'strict ' : '';
        $parsedGraph .= $this->getDirected() ? 'digraph ' : 'graph ';
        $parsedGraph .= $this->_escape($this->graph['name']) . " {\n";

        $indent = '    ';

        $attr = $this->_escapeArray($this->graph['attributes']);
        foreach($attr as $key => $value)
        {
            $parsedGraph .= $indent . $key . '=' . $value . ";\n";
        }

        $groups = $this->_getGroups();
        foreach($this->graph['nodes'] as $group => $nodes)
        {
            if(!in_array($group, $groups)) {
                $parsedGraph .= $this->_nodes($nodes, $indent);
            }
        }
        $tops = $this->_getTopGraphs();
        foreach($tops as $group)
        {
            $parsedGraph .= $this->_subgraph($group, $indent);
        }

        if($this->getDirected()) {
            $separator = ' -> ';
        } else {
            $separator = ' -- ';
        }

        foreach($this->graph['edgesFrom'] as $from => $toNodes)
        {
            $from = $this->_escape($from);

            foreach($toNodes as $to => $edges)
            {
                $to = $this->_escape($to);

                foreach($edges as $info)
                {
                    $f = $from;
                    $t = $to;

                    if(array_key_exists('portFrom', $info)) {
                        $f .= ':' . $this->_escape($info['portFrom']);
                    }

                    if(array_key_exists('portTo', $info)) {
                        $t .= ':' . $this->_escape($info['portTo']);
                    }

                    $parsedGraph .= $indent . $f . $separator . $t;

                    if(!empty($info['attributes'])) {
                        $attributeList = array();

                        foreach($this->_escapeArray($info['attributes']) as $key => $value)
                        {
                            switch($key)
                            {
                                case 'lhead':
                                case 'ltail':
                                    if(strncasecmp($value, 'cluster', 7)) {
                                        $value = 'cluster_' . $value;
                                    }
                                    break;
                            }
                            $attributeList[] = $key . '=' . $value;
                        }

                        $parsedGraph .= ' [ ' . implode(',', $attributeList) . ' ]';
                    }

                    $parsedGraph .= ";\n";
                }
            }
        }
        return $parsedGraph . "}\n";
    }

    /**
     * Output nodes
     *
     * @param array  $nodes  nodes list
     * @param string $indent space indentation
     *
     * @return string output
     * @access protected
     */
    function _nodes($nodes, $indent)
    {
        $parsedGraph = '';
        foreach($nodes as $node => $attributes)
        {
            $parsedGraph .= $indent . $this->_escape($node);

            $attributeList = array();

            foreach($this->_escapeArray($attributes) as $key => $value)
            {
                $attributeList[] = $key . '=' . $value;
            }

            if(!empty($attributeList)) {
                $parsedGraph .= ' [ ' . implode(',', $attributeList) . ' ]';
            }

            $parsedGraph .= ";\n";
        }
        return $parsedGraph;
    }

    /**
     * Generates output for a group
     *
     * @return string output
     * @access protected
     */
    function _subgraph($group, &$indent)
    {
        $parsedGraph = '';
        $nodes = $this->graph['nodes'][$group];

        if($group !== 'default') {
            $type = null;
            $_group = $this->_escape($group);

            if(isset($this->graph['clusters'][$group])) {
                $type = 'clusters';
                if(strncasecmp($group, 'cluster', 7)) {
                    $_group = $this->_escape('cluster_' . $group);
                }
            } elseif(isset($this->graph['subgraphs'][$group])) {
                $type = 'subgraphs';
            }
            $parsedGraph .= $indent . 'subgraph ' . $_group . " {\n";

            $indent .= '    ';

            if($type !== null && isset($this->graph[$type][$group])) {
                $cluster = $this->graph[$type][$group];
                $_attr = $this->_escapeArray($cluster['attributes']);

                $attr = array();
                foreach($_attr as $key => $value)
                {
                    $attr[] = $key . '=' . $value;
                }

                if(strlen($cluster['title'])) {
                    $attr[] = 'label='
                            . $this->_escape($cluster['title'], true);
                }

                if($attr) {
                    $parsedGraph .= $indent . 'graph [ ' . implode(',', $attr)
                            . " ];\n";
                }
            }
        }

        $parsedGraph .= $this->_nodes($nodes, $indent);

        foreach($this->_getSubgraphs($group) as $_group)
        {
            $parsedGraph .= $this->_subgraph($_group, $indent);
        }

        if($group !== 'default') {
            $indent = substr($indent, 0, -4);

            $parsedGraph .= $indent . "}\n";
        }

        return $parsedGraph;
    }

    /**
     * Saves GraphViz markup to file (in DOT language)
     *
     * @param string $file File to write the GraphViz markup to.
     *
     * @return string File to which the GraphViz markup was written, FALSE or
     *                or self_Error on failure.
     * @access public
     */
    function saveParsedGraph($file = '')
    {
        $parsedGraph = $this->parse();
        if(!empty($parsedGraph)) {
            if(empty($file)) {
                $file = tempnam(sys_get_temp_dir(), 'CIDC_');
            }
            file_put_contents($file, $parsedGraph);
            return $file;
        }

        if($this->_returnFalseOnError) {
            return false;
        }
        $error = new Exception('Could not save graph');
        return $error;
    }

    /**
     * Tell whether a value is a self error.
     *
     * @param   mixed $data   the value to test
     * @param   int   $code   if $data is an error object, return true
     *                        only if $code is a string and
     *                        $obj->getMessage() == $code or
     *                        $code is an integer and $obj->getCode() == $code
     * @access  public
     * @return  bool    true if parameter is an error
     */
    function isError($data, $code = null)
    {
        if(!is_a($data, 'Error')) {
            return false;
        }

        if(is_null($code)) {
            return true;
        } elseif(is_string($code)) {
            return $data->getMessage() == $code;
        }

        return $data->getCode() == $code;
    }

}
