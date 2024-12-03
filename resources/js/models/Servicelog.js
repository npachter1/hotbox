import Model from './Model'

export default class Servicelog extends Model {
    
    resource(){
        return 'admin/auth/servicelogs';
    }
  
    primaryKey(){
        return 'id';
    }
    
}