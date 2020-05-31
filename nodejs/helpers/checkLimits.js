/**
 * This file will check limitations on the users
 * one limitation is configured Mana
 * and another limitation is effective SP of each upvote
 */
const call = require('./nodeCall')
const config = require('../config')
const con = require('../mysql')
const isSteemd = config.isSteemd
// we are using isSteemd to change methods for appbase

let tvfs
let tvs
const updateGlobals = async () => {
  try {
    const result = await call(
      isSteemd ? config.steemd : config.rpc,
      isSteemd ? 'condenser_api.get_dynamic_global_properties' : 'get_dynamic_global_properties',
      []
    )
    // on any error, result will be null
    if (!result) return
    tvfs = parseInt(result.total_vesting_fund_steem.replace('STEEM', ''))
    tvs = parseInt(result.total_vesting_shares.replace('VESTS', ''))
  } catch (e) {
    throw new Error(e)
  }
}
updateGlobals()
setInterval(() => {
  updateGlobals()
}, 900000)

// we will check limitations then broadcast upvote
const checkpowerlimit = async (voter, author, permlink, weight) => {
  try {
    const results = await con.query(
      'SELECT `limit_power` FROM `users` WHERE `user`=?',
      [voter]
    )
    const powerlimit = results[0].limit_power
    const result = await call(
      isSteemd ? config.steemd : config.rpc,
      isSteemd ? 'condenser_api.get_accounts' : 'get_accounts',
      [
        [voter]
      ]
    )
    // on any error, result will be null
    if (!result) return null
    if (tvfs && tvs) {
      const u = result[0]
      // calculating total SP to check against limitation
      const delegated = parseInt(u.delegated_vesting_shares.replace('VESTS', '')) // VESTS
      const received = parseInt(u.received_vesting_shares.replace('VESTS', '')) // VESTS
      const vesting = parseInt(u.vesting_shares.replace('VESTS', '')) // VESTS
      const totalvest = vesting + received - delegated
      let sp = totalvest * (tvfs / tvs)
      sp = sp.toFixed(2)
      // calculating Mana to check against limitation
      let withdrawRate = 0
      if (parseInt(u.vesting_withdraw_rate.replace('VESTS', '')) > 0) {
        withdrawRate = Math.min(
          parseInt(u.vesting_withdraw_rate.replace('VESTS', '')),
          parseInt((u.to_withdraw - u.withdrawn) / 1000000)
        )
      }
      let maxMana = Number((totalvest - withdrawRate) * Math.pow(10, 6))
      let delta = Date.now() / 1000 - u.voting_manabar.last_update_time
      let currentMana = Number(u.voting_manabar.current_mana) + (delta * maxMana / 432000)
      let percentage = Math.round(currentMana / maxMana * 10000)
      if (!isFinite(percentage)) percentage = 0
      if (percentage > 10000) percentage = 10000
      else if (percentage < 0) percentage = 0
      let powernow = (percentage / 100).toFixed(2)
      if (powernow > powerlimit) {
        if (((powernow / 100) * (weight / 10000) * sp) > 3) {
          // Don't broadcast upvote if sp*weight*power < 3
          return 1
        }
        return null
      }
      return null
    }
  } catch (e) {
    return null
  }
}

module.exports = checkpowerlimit
