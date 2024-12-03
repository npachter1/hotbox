import Model from './Model'

export default class Receiving extends Model {
    
    resource(){
        return 'admin/dispensary/receivings';
    }
  
    primaryKey(){
        return 'id';
    }
    
}