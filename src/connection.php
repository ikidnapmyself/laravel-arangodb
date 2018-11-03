<?php namespace LaravelFreelancerNL\Aranguent;

use ArangoDBClient\Connection as ArangoConnection;
use ArangoDBClient\ConnectionOptions as ArangoConnectionOptions;
use ArangoDBClient\CollectionHandler as ArangoCollectionHandler;
use ArangoDBClient\DocumentHandler as ArangoDocumentHandler;
use ArangoDBClient\GraphHandler as ArangoGraphHandler;
use Illuminate\Database\Connection as IlluminateConnection;
use LaravelFreelancerNL\Aranguent\Schema\Grammars\Grammar;


class Connection extends IlluminateConnection {

    protected $arangoConnection;

     /**
     * @inheritdoc
     *
     * @var array
     */
    protected $defaultConfig = array(
        ArangoConnectionOptions::OPTION_ENDPOINT => 'tcp://localhost:8529',
        ArangoConnectionOptions::OPTION_CONNECTION  => 'Keep-Alive',
        ArangoConnectionOptions::OPTION_DATABASE => null,
        ArangoConnectionOptions::OPTION_AUTH_USER => null,
        ArangoConnectionOptions::OPTION_AUTH_PASSWD => null,
        'tablePrefix' => ''
    );

    protected $collectionHandler;

    protected $documentHandler;

    protected $graphHandler;

    protected $userHandler;

    /**
     * The ArangoDB driver name
     *
     * @var string
     */
    protected $driverName = 'arangodb';

    /**
     * Connection constructor.
     * @param string $database
     * @param string $tablePrefix
     * @param array $config
     */
    public function __construct(array $config = array())
    {

        // First we will setup the default properties. We keep track of the DB
        // name we are connected to since it is needed when some reflective
        // type commands are run such as checking whether a table exists.
        $this->config = array_merge($this->defaultConfig, $config);

        $this->database = $this->config ['database'];

        $this->tablePrefix = $this->config['tablePrefix'];

        // activate and set the database client connection
        $this->arangoConnection = new ArangoConnection($this->config);

        $this->collectionHandler = new ArangoCollectionHandler($this->arangoConnection);
        $this->documentHandler = new ArangoDocumentHandler($this->arangoConnection);
        $this->graphHandler = new ArangoGraphHandler($this->arangoConnection);

        // We need to initialize a query grammar and the query post processors
        // which are both very important parts of the database abstractions
        // so we initialize these to their default values while starting.
        $this->useDefaultQueryGrammar();

        $this->useDefaultPostProcessor();
    }

    public function setSchemaGrammar(Grammar $grammar)
    {
        $this->schemaGrammar = $grammar;

        return $this;
    }


    public function getArangoConnection()
    {
        return $this->arangoConnection;
    }

    public function getCollectionHandler()
    {
        return $this->collectionHandler;
    }

    public function getDocumentHandler()
    {
        return $this->documentHandler;
    }

    public function setUserHandler($userHandler)
    {
        $this->userHandler = $userHandler;
    }
    public function getUserHandler()
    {
        return $this->userHandler;
    }

    public function getGraphHandler()
    {
        return $this->graphHandler;
    }

}