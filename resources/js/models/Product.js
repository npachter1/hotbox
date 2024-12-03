import Model from './Model'

export default class Product extends Model {
    
    resource(){
        return 'admin/dispensary/products';
    }
  
    primaryKey(){
        return 'id';
    }
    
}