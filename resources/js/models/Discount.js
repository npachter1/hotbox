import Model from './Model'

export default class Discount extends Model {
    
    resource(){
        return 'admin/dispensary/discounts';
    }
  
    primaryKey(){
        return 'id';
    }
    
}