<?php

    namespace Palvoelgyi\Mongo;

    use Exception;
    use MongoDB\Client;
    use MongoDB\Model\CollectionInfo;
    use MongoDB\Model\CollectionInfoCommandIterator;
    use Palvoelgyi\Helper\Helper;

class Mongo
    {
        public Client $client;
        private $db;
        private $table;
        public  $collections = [];
        private $searchArray = [];
        private int $limit   = 0;

        /**
         * 
         * TODO: indexierung: db.user.createIndex({name:1})
         *      sort: db.user.find({ name: /^Estella/ }, { name:1, _id:0 } } ).sort( { name:-1, "comments.comment":1 } );
         */

        public function __construct()
        {
           
            if ( !extension_loaded ('mongodb') ) {

                throw new Exception('The extension "extension = php_mongodb.dll" must be loaded into php.ini. Do not forget to reload the server afterwards!');

                exit;
            }

            if( ! isset ( $this->client ) ) {

                $this->client = new Client;
            }
        }

        public function setDb(string $db) : Mongo 
        {
            $this->db = $db;

            return  $this;
        }

        public function setTable(string $table) : Mongo
        {
            $this->table = $table;

            return  $this;
        }

        public function addIndex( array $index) : Mongo 
        {            
            if( empty( $index ) ) {  return $this; }
            
            $db    = $this->db;
            $table = $this->table;

            $this->client->$db->$table->createIndex($index);

            return $this;
        }

        public function dropIndex( string $index) : Mongo 
        {            
            if( empty( $index ) ) {  return $this; }
            
            $db    = $this->db;
            $table = $this->table;

            $this->client->$db->$table->dropIndex($index);

            return $this;
        }

        public function getIndexes() : array
        {

            $db    = $this->db;
            $table = $this->table;

            foreach ($this->client->$db->$table->listIndexes() as $index) {

                $name = array_keys ( $index['key'] )[0];

                $listIndexes[$name] = $name;
             }

             return $listIndexes;
        }

        public function setLimit(int $limit) : Mongo 
        {
            $this->limit = $limit;

            return  $this;
        }

        public function getClient() : Client
        {
            return $this->client;
        }

        public function getCollectionsName( $db = null ) : array
        {
            if( is_null( $db ) AND empty( $this->db ) ) { return []; }
            elseif( !is_null($db) ){ $this->db = $db; }

            $db = $this->db;

            $colecciones = $this->client->$db->listCollections();

            foreach ($colecciones as $col) {

                $this->collections[] = $col->getName();
            }

            return $this->collections;
        }

        public function getCollections( $db = null, $data = 0 ) : array
        {
            $limit  = [];

            if( $this->limit > 0 ){ $limit  = [ 'limit' => $this->limit ]; }

            if( is_null( $db ) AND empty( $this->db ) ) { return []; }
            elseif( !is_null($db) ){ $this->db = $db; }

            $db = $this->db;

            $colecciones = $this->client->$db->listCollections();

            foreach ($colecciones as $col) {

                $getName = $col->getName();

                if( $data === 0 ){

                    $this->collections[ $getName ] = $col;

                }else{

                    $this->collections[ $getName ]['CollectionInfo'] = $col;
                } 
            }

            return $this->collections;
        }

        public function getCollectionTableData( $searchArray = [], $db = null, $withCount = true ) : array
        {
            if( is_null( $db ) AND empty( $this->db ) ) { return []; }

            $db    = $this->db;
            $table = $this->table;
            $data  = $limit = []; 

            if( !empty( $searchArray ) ) { $this->searchArray = $searchArray; }

            if( $this->limit > 0 ) { $limit = [ 'limit' => $this->limit ]; }

            $cursor = $this->client->$db->$table->find( $this->searchArray, $limit );

            $counter = 0;

            foreach ($cursor as $row) {

                foreach( $row AS $key => $das ) {

                    $data[ $counter ][ $key ] = $das;
                }

                $counter++;
             };

             if( $withCount == true ){ array_unshift( $data, count($data) ); }
             
            return $data;
        }

        public function getDatabases( $withcount = 0 ) : array
        {
            $databases = [];
            
            foreach ( $this->client->listDatabases() as $databaseInfo ) {
               
                $name       = $databaseInfo['name'];
                $sizeOnDisk = $databaseInfo['sizeOnDisk'];
                $empty      = $databaseInfo['empty'];
                
                $databases[$name]['name']       = $name;
                $databases[$name]['SizeOnDisk'] = $sizeOnDisk;
                $databases[$name]['empty']      = ($empty) ? 'true' : 'false';
            }
            
            if( $withcount == 1 ) $databases = [ 'count' => count($databases) ] + $databases;
            if( $withcount == 2 ) $databases = [ 'count' => count($databases) ];

            

            return $databases;

        }
    }
