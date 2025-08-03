<?php
namespace App\Geonames\Domain\Import\Sql;

class ImportSql
{

    /**
     * @throws \Exception
     */
    public static function loadSql(object $db, string $file): void
    {
        if(file_exists($file)){
            $sql = trim(file_get_contents($file));
            if($sql){
                $db->beginTransaction();
//                $count = 0;
//                try{
                    $querySet = $db->prepare($sql);
                    $querySet->execute();
//                    while($querySet->nextRowSet()){
//                        ++$count;
//                    }
//                }catch(\Exception $e){
//                    $db->rollBack();
//                    throw $e;
//                }
            }else{
                throw new \Exception("File {$file} appears to be empty.");
            }
        }else{
            throw new \Exception("File '{$file}' not found.");
        }
        unset($sql);
    }
}