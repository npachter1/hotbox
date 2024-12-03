import Model from './Model'

export default class Campaign extends Model {
    
    resource(){
        return 'admin/dispensary/campaigns';
    }
  
    primaryKey(){
        return 'id';
    }
    
}