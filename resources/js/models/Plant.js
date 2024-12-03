import Model from './Model'

export default class Plant extends Model {
    
    resource(){
        return 'admin/grow/plants';
    }
  
    primaryKey(){
        return 'id';
    }
    
}