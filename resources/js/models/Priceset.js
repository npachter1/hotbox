import Model from './Model'

export default class Priceset extends Model {
    
    resource(){
        return 'admin/dispensary/pricesets';
    }
  
    primaryKey(){
        return 'id';
    }
    
}