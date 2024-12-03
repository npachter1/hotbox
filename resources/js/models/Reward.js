import Model from './Model'

export default class Reward extends Model {
    
    resource(){
        return 'admin/dispensary/rewards';
    }
  
    primaryKey(){
        return 'id';
    }
    
}