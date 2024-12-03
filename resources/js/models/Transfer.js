import Model from './Model'

export default class Transfer extends Model {
    
    resource(){
        return 'admin/grow/transfers';
    }
  
    primaryKey(){
        return 'id';
    }
    
}