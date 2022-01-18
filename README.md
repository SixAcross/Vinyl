
# Vinyl

A relational data mapper library


## Usage

A brief demo of how to use RecordStores (the repository/mapper) and Records (the entity/model) to CRUD data.

    use PDO;
    use Doctrine\DBAL\DriverManager;
    use Doctrine\DBAL\Configuration;
    use BitBalm\Vinyl\V1 as Vinyl;
    
    $pdo = new PDO( mysql:dbname=vinyl', 'vinyl', 'some_password' );
    $connection = DriverManager::getConnection( [ 'pdo' => $pdo, ], new Configuration );
    
    // see Vinyl\V1\RecordStore for the full interface
    $user_store = new Vinyl\RecordStore\SQL\PDO\Doctrine( 
        'users', 
        $connection->createQueryBuilder(),
        new Vinyl\RecordProducer\PDO\Statement( new Vinyl\Record\Generic )
      );
    
    
    // see Vinyl\V1\Record for the full interface
    $user = $user_store->insertRecord([ 'first_name' => 'June', 'last_name' => 'Roderick', ]);
    
    $user = $user_store->getRecord( 99 );
    
    $user['last_name'] = 'Rodriguez';
    
    $user_store->updateRecord( $user );
    
    $user_store->deleteRecord( $user );
    
    
    
    
    
