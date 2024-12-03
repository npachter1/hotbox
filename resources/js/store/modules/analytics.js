// startDate/endDate are moment objects, but if the vuex store is persisted to localStorage (vuex-persisted), the moment object is serialized to an ISO string
// to get around this, access the date variables via getters which will make sure they return moment object (or null if not set)

const state = {
    startDate: null,
    endDate: null,
    locationId: null,
    period: null
};

const getters = {
    getStartDate(state) {
        const startDate = state.startDate;
        if (startDate) return moment(startDate);
        return null;
    },
    getEndDate(state) {
        const endDate = state.endDate;
        if (endDate) return moment(endDate);
        return null;
    },
    salesAmount: (state) => () => { //return a function: https://forum.vuejs.org/t/vuex-getter-not-re-evaluating-return-cached-data/55697/5
        const ONE_HOUR = 60 * 60 * 1000; /* ms */
        if (state.salesAmount && ((new Date())-(new Date(state.salesAmount.date))<ONE_HOUR)) {
            return state.salesAmount.value;
        }
        return null;
    },
    loyaltyAmount: (state) => () => { //return a function: https://forum.vuejs.org/t/vuex-getter-not-re-evaluating-return-cached-data/55697/5
        const ONE_HOUR = 60 * 60 * 1000; /* ms */
        if (state.loyaltyAmount && ((new Date())-(new Date(state.salesAmount.date))<ONE_HOUR)) {
            return state.loyaltyAmount.value;
        }
        return null;
    }
};

const mutations = {
    setFilters(state, payload) {
        state.startDate = payload.startDate;
        state.endDate = payload.endDate;
        state.locationId = payload.locationId;
        state.period = payload.period;
    },
    setDashboardMetrics(state, payload) {
        if (payload.salesAmount) state.salesAmount={date:(new Date()).toUTCString(), value: payload.salesAmount};
        if (payload.loyaltyAmount) state.loyaltyAmount={date: (new Date()).toUTCString(), value: payload.loyaltyAmount};
    }
};

const actions = {

};

export default {
    state,
    mutations,
    actions,
    getters,
    namespaced: true
};