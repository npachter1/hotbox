import Model from './Model'

export default class Location extends Model {
    
    resource(){
        return 'admin/auth/location';
    }
  
    primaryKey(){
        return 'id';
    }
    
}