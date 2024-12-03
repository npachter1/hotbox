import Model from './Model'

export default class Harvest extends Model {
    
    resource(){
        return 'admin/grow/harvests';
    }
  
    primaryKey(){
        return 'id';
    }
    
}