<template>
    <!-- <div class="container"> -->
        <div class="row">
            <div class="col-12 text-center">
                <div class="activity-content">
                    <div class="row">
                        <div class="col-12">
                            <h2 class="decorated-center"><span>{{ i18n.project_activity }}</span></h2>
                        </div>
                    </div>
                    <!-- <pre>
                        {{activitiesObject}}
                    </pre> -->
                    <div v-for="(value, key) in activitiesObject">
                    <hr>
                        {{ key }}
                        <hr>
                        <div v-for="v in value">
                            {{v.activity_type}}
                        </div>
                    </div>
                    <!-- <div class="row" v-for="(activity, index) in activities">
                        <div class="col-2">
                            <span>{{activity.formatted_date}}</span>
                        </div>
                        <div class="col-2">
                            <span>{{activity.formatted_time}}</span>
                        </div>
                        <div class="col-8">
                            <activity-info :activity="activity" :i18n="i18n"></activity-info>
                        </div>
                    </div> -->
                    
                    <div class="row" v-if="currentCount < totalActivityCount">
                        <div class="col-12">
                            <button class="button" @click="loadMoreActivities">Load More</button>
                        </div>
                    </div>
                    <div v-if="noActivity">
                        {{ i18n.no_activity_yet }}
                    </div>
                </div>
            </div>
            </div>
        </div>
    <!-- </div> -->
</template>

<style>
.activity-content {
    padding: 35px 20px;
    background: #fff;
}
.activity-avatar {
    float: left;
    margin-right: 10px;
    margin-top: 5px;
}
</style>

<script>
    import ActivityInfo from './partials/ActivityInfo.vue';
    export default {
        components: {
            ActivityInfo
        },

        props: [
            'i18n'
        ],

        data () {
            return {
                activities: [],
                totalActivityCount: '',
                noActivity: false,
                activitiesObject : {},
                currentCount: ''
            }
        },

        methods: {
            fetchActivities() {
                var vm = this;

                var data = {
                    action: 'fpm-get-activities',
                    project_id: vm.$route.params.projectid,
                    nonce: fpm.nonce,
                };

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    // if ( resp.success ) {
                    //     console.log(Object.values(resp.data.groupBy('formatted_date')));
                    //     vm.activities = resp.data.groupBy('formatted_date');
                    //     console.log(vm.activities);
                    //     vm.totalActivityCount = resp.data[0].total_activity;

                    //     if (vm.totalActivityCount < 1) {
                    //         vm.noActivity = true;
                    //     }
                    // }
                    if (resp.success) {
                        var activities = [],
                            current,
                            next,
                            i,
                            length = resp.data.length,
                            keys;

                        vm.currentCount = length;
                        vm.totalActivityCount = resp.data[0].total_activity;
                        for(i = 0; i < length; i++ ) {

                            current = resp.data[i],
                            next = resp.data[i+1]; 
                            if(!next) {
                                console.log('!next');
                                activities = [];
                                if(resp.data[i].formatted_date === resp.data[i-1].formatted_date) {
                                    keys = Object.keys(vm.activitiesObject);
                                    vm.activitiesObject[keys[keys.length-1]].push(resp.data[i]);
                                } else {
                                    activities.push(resp.data[i]);
                                    vm.$set(vm.activitiesObject, resp.data[i].formatted_date, activities);
                                }
                                continue;
                            };

                            if(current.formatted_date === next.formatted_date) {
                                activities.push(resp.data[i]);
                                if((i+2) === length) {
                                    vm.$set(vm.activitiesObject, resp.data[i].formatted_date, activities);
                                    activities = [];
                                }
                            } else {
                                activities.push(resp.data[i]);
                                vm.$set(vm.activitiesObject, resp.data[i].formatted_date, activities);
                                activities = [];    
                            }
                        }
                    }
                });
            },

            loadMoreActivities() {
                var vm = this;
                vm.loadMore = true;
                var data = {
                    action: 'fpm-load-more-activities',
                    nonce: fpm.nonce,
                    offset: vm.currentCount,
                    project_id: vm.$route.params.projectid
                };

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    vm.loadMore = false;
                    // if ( resp.success ) {
                    //     vm.activityCount = vm.activityCount + resp.data[0].record_count;
                    //     for(var i = 0; i < resp.data.length; i++ ) {
                    //         vm.activities.push(resp.data[i]);
                    //     }
                    // }
                    
                    if (resp.success) {
                        var activities = [],
                            keys = Object.keys(vm.activitiesObject),
                            previous = vm.activitiesObject[keys[keys.length-1]],
                            i,
                            current,
                            next,
                            length = resp.data.length;

                        vm.currentCount += length;
                        console.log(previous);
                        console.log(resp.data);

                        for(i = 0; i < length; i++ ) {

                            current = resp.data[i],
                            
                            console.log(i);
                            // console.log(previous[0].formatted_date);
                            console.log(current.formatted_date);
                            console.log(current.activity_type);

                            if(previous[0].formatted_date === current.formatted_date) {
                                console.log('previous===current');
                                previous.push(current);
                                continue;
                            }

                            next = resp.data[i+1]; 
                            if(!next) {
                                console.log('!next');
                                activities = [];
                                if(resp.data[i].formatted_date === resp.data[i-1].formatted_date) {
                                    keys = Object.keys(vm.activitiesObject);
                                    vm.activitiesObject[keys[keys.length-1]].push(resp.data[i]);
                                } else {
                                    activities.push(resp.data[i]);
                                    vm.$set(vm.activitiesObject, resp.data[i].formatted_date, activities);
                                }
                                continue;
                            };

                            if(current.formatted_date === next.formatted_date) {
                                console.log('next===current');
                                activities.push(resp.data[i]);
                                if((i+2) === length) {
                                    vm.$set(vm.activitiesObject, resp.data[i].formatted_date, activities);
                                    activities = [];
                                }
                            } else {
                                activities.push(resp.data[i]);
                                vm.$set(vm.activitiesObject, resp.data[i].formatted_date, activities);
                                activities = [];
                            }
                        }

                        console.log(vm.activitiesObject);
                    }
                });
            },
        },

        created() {
            this.fetchActivities();
        }
    }
</script>