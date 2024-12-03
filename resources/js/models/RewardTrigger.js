import Model from './Model'

export default class RewardTrigger extends Model {
    
    resource(){
        return 'admin/dispensary/rewardstriggers';
    }
  
    primaryKey(){
        return 'id';
    }
    
}