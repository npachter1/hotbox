import Model from './Model'

export default class PlantBatch extends Model {
    
    resource(){
        return 'admin/grow/plantbatches';
    }
  
    primaryKey(){
        return 'id';
    }
    
}