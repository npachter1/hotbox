import Model from './Model'

export default class Group extends Model {
    
    resource(){
        return 'admin/dispensary/groups';
    }
  
    primaryKey(){
        return 'id';
    }
    
}