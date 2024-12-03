import Model from './Model'

export default class Addressbook extends Model {
    
    resource(){
        return 'admin/auth/addressbook';
    }
  
    primaryKey(){
        return 'id';
    }
    
}