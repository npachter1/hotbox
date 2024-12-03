import Model from './Model'

export default class Drawer extends Model {
    
    resource(){
        return 'admin/dispensary/drawers';
    }
  
    primaryKey(){
        return 'id';
    }
    
}