import Model from './Model'

export default class CustomerQueue extends Model {
    
    resource(){
        return 'admin/dispensary/customersqueue';
    }
  
    primaryKey(){
        return 'id';
    }
    
}