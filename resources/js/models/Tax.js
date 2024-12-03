import Model from './Model'

export default class Tax extends Model {
    
    resource(){
        return 'admin/dispensary/taxes';
    }
  
    primaryKey(){
        return 'id';
    }
    
}