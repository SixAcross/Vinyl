<?php

namespace BitBalm\Relator\PDO;


interface InvalidTable {}

interface InvalidColumn {}

interface PrimaryKeyNotFound {}


interface SchemaValidator
{
    public function isValidTable( string $table ) : bool ;
    
    public function validTable( string $table ) : string ;
    
    public function isValidColumn( string $table, string $column ) : bool ;
    
    public function validColumn( string $table, string $column ) : string ;
    
    public function getPrimaryKeyName( string $table ) : string ;
}
