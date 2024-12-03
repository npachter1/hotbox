import Model from './Model'

export default class Locations extends Model {
    
    resource(){
        return 'admin/auth/locations';
    }
  
    primaryKey(){
        return 'id';
    }
    
}