import Model from './Model'

export default class TaxRate extends Model {
    
    resource(){
        return 'admin/dispensary/taxrates';
    }
  
    primaryKey(){
        return 'id';
    }
    
}