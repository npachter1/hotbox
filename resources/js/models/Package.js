import Model from './Model'

export default class Package extends Model {
    
    resource(){
        return 'admin/grow/packages';
    }
  
    primaryKey(){
        return 'id';
    }
    
}