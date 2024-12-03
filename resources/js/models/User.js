import Model from './Model'

export default class User extends Model {
    
    resource(){
        return 'admin/auth/users';
    }
  
    primaryKey(){
        return 'id';
    }
    
}