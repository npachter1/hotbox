import Model from './Model'

export default class Item extends Model {
    
    resource(){
        return 'admin/grow/items';
    }
  
    primaryKey(){
        return 'id';
    }
    
}