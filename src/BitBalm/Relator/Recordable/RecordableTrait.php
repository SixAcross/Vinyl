<?php

namespace BitBalm\Relator\Recordable;

use Exception;

use BitBalm\Relator\Record;
use BitBalm\Relator\Record\RecordTrait;
use BitBalm\Relator\Recordable;
use BitBalm\Relator\Recorder;
use BitBalm\Relator\RecordSet;


Trait RecordableTrait
{    
    protected static $primary_key_name;
    protected static $recorder;
    
    protected $recorder_update_id;
    
    
    public function setRecorder( Recorder $recorder ) : Recordable
    {
        if ( self::$recorder and self::$recorder !== $recorder ) {
            throw new InvalidArgumentException("This record's Recorder is already set. ");
        }
        
        self::$recorder = $recorder;
        
        return $this;
    }
    
    public function getRecorder() : Recorder 
    {
        #TODO: throw Exception instead of TypeError when not set?
        return self::$recorder;
    }
    
    
    public function loadRecord( $record_id ) : Recordable 
    {
        return $this->getRecorder()->loadRecord( $this, $record_id );
    }

    public function loadRecords( array $record_ids ) : RecordSet 
    {
        return $this->getRecorder()->loadRecords( $this, $record_ids );
    }
    
    public function saveRecord() : Recordable 
    {
        return $this->getRecorder()->saveRecord($this);
    }
    
    public function deleteRecord() 
    {
        return $this->getRecorder()->deleteRecord($this);
    }
    
    
    public function setUpdateId( $id ) : Recordable
    {
        $this->recorder_update_id = $id ;
        return $this;
    }
    
    public function getUpdateId() 
    {
        return $this->recorder_update_id ?? null ;
    }
    
    public function getPrimaryKeyName() : string
    {
        #TODO: throw Exception instead of TypeError when not set?
        return static::$primary_key_name;
    }
    
    public function setPrimaryKeyName( string $primary_key_name ) : Recordable
    {
        $existing_name = static::$primary_key_name;
        
        if ( $existing_name === $primary_key_name ) { return $this; }
        
        if ( !is_null($existing_name) )  {
            throw new InvalidArgumentException(
                "The primary key name for this object is already set to: {$existing_name}. "
              );
        }
        
        static::$primary_key_name = $primary_key_name;
        
        return $this;
        
    }
}
