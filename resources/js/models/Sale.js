import Model from './Model'

export default class Sale extends Model {
    
  resource(){
      return 'admin/dispensary/sales';
  }
  
  primaryKey(){
      return 'id';
  }

  

    
}