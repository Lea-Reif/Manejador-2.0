<?php namespace App\Models;

use CodeIgniter\Model;

class Manejador extends Model
{
    function getDb(){
        $str = file_get_contents(APPPATH.'../json/db.json');
            $json = json_decode($str);
            foreach ($json as $key => $db) {
                        $db->id= $key;
                    }
        return $json;
     }
    
     function getConsultas(){
        
        $str = file_get_contents(APPPATH.'../json/consultas.json');
        $json = json_decode($str);
        foreach ($json as $key => $db) {
                    $db->id= $key;
    
                }
        
       return json_encode($json);
     }
    
     function getGroups(){
        
       $str = file_get_contents(APPPATH.'../json/grupos.json');
       $json = json_decode($str);
       foreach ($json as $key => $db) {
                   $db->id= $key;
    
             }
        
       return json_encode($json);
     }
     function saveConsulta($data){
    
        $consultas = file_get_contents(APPPATH.'../json/consultas.json');  
        $consultas_array = json_decode($consultas, true);  
        $extra = $data;
        $consultas_array[] = $extra;  
        $final_data = json_encode($consultas_array);
        file_put_contents(APPPATH.'../json/consultas.json', $final_data);
    
    
        return print_r($final_data);
     
     }
    
       function updateGroups($data)
       {
          extract($data);
          $dbs = json_decode($this->getDb());
          foreach ($dbs as $key => $db) {
             if(in_array($db->id,$ids))$dbs[$key]->group = $new;
                unset($dbs[$key]->id);
          }
          $dbs = json_encode($dbs);
          file_put_contents(APPPATH.'../json/db.json', $dbs);
       }
}