# mongodb

    use Palvoelgyi\Mongo\Mongo;

    $mongo = new Mongo;

# die normale MongoDB Client 
   
$client = $mongo->getClient();

# [0] => __construct

# [1] => setDb

$mongo->setDb('test');

# [2] => setTable

$mongo->setTable('user');

# [3] => addIndex

$mongo->addIndex( [ 'name' => 1 ] );

# [4] => dropIndex

$mongo->dropIndex( 'name_1' );

# [5] => getIndexes

 Helper::e( $mongo->getIndexes());

# [6] => setLimit

$mongo->setDb('test')
    ->setTable('user')
    ->setLimit(20);

# [7] => getClient

$client = $mongo->getClient();


# [8] => getCollectionsName

var_dump( $mongo->getCollectionsName() );

# [9] => getCollections

var_dump( $mongo->getCollections() );

# [10] => getCollectionTableData

var_dump( $mongo->getCollectionTableData() );

# [11] => getDatabases

var_dump( $mongo->getDatabases() );