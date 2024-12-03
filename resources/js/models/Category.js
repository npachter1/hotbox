import Model from './Model'

export default class Category extends Model {
    
    resource(){
        return 'admin/dispensary/categories';
    }
  
    primaryKey(){
        return 'id';
    }
    
}