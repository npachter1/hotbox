import Model from './Model'

export default class Mcategory extends Model {
    
    resource(){
        return 'admin/dispensary/categoriesmetrc';
    }
  
    primaryKey(){
        return 'id';
    }
    
}