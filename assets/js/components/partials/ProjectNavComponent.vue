<template>
    <div v-if="!isClient">
        <div class="project-navigation">
            <div class="nav-link">
                <span style="display:inline-block" @click="toggleNavSection">
                    <i class="fa fa-bars" aria-hidden="true"></i>
                </span>
                <router-link :to="'/projects/' + $route.params.projectid" tag="div" class="link-style" style="display:inline-block">
                    <a style="font-weight:bold;font-size:16px;padding-left:5px;">
                        {{project.project_title | truncate('20')}}
                    </a>
                </router-link>
                <slot></slot>
                <!-- <div v-if="+project.is_archive" class="archive-label">
                    Archived Project
                </div> -->
            </div>
        </div>
    </div>
</template>

<script>
    export default{
        props: [ 'projecttitle', 'navname', 'summary' ],
        data() {
            return {
                project: '',
                isShowNavSection: false,
                currentUser: '',
                isClient: false
            }
        },
        filters: {
            truncate: function(string, value) {
                var dotdot = '';
                if(!string)
                    string = '';
                if (string.length > value) {
                    dotdot = '...';
                }
                return string.substring(0, value) + dotdot;
            }
        },
        methods: {
            toggleNavSection() {
                this.isShowNavSection = !this.isShowNavSection;
            },
            fetchProjectInfo: function() {
                var vm = this;

                var data = {
                    action: 'fpm-get-project',
                    project_id: vm.$route.params.projectid,
                    nonce: fpm.nonce,
                };
                if ( vm.summary ) {
                    data.is_summary = vm.summary
                }

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    
                    if ( resp.success ) {
                        vm.project = resp.data[0];
                        vm.$emit( 'get-project', vm.project );
                    } else {
                        vm.$router.push({
                            path: `/?item=Project&op=rf`
                        });
                    }
                });
            },
        },
        created() {
            var vm = this,
                isClientUser;

            vm.currentUser = fpm.currentUserInfo;
            vm.fetchProjectInfo();
            
            isClientUser = vm.currentUser.roles.includes('fpm_client');
            if(isClientUser) {
                if(vm.navname !== 'inbox') {
                    vm.$router.push('/');
                }
                vm.isClient = true;
            }
        }
    }
</script>

<style>
    .project-navigation {
        text-align: center;
        background: #fff;
        margin-left: 30px;
        margin-right: 30px;
        padding: 8px;
        border-radius: 5px 5px 0px 0px;

    }
    .border-for-nav {
        border-top: 1px solid #eee;
    }
    .nav-section {
        padding: 5px;
        border: 1px solid #eee;
        border-radius: 4px;
        min-width: 84px;
        display: inline-block;
        margin: 10px;
    }
    .nav-link {
        margin-top: 5px;
        margin-bottom: 5px;
    }
    .archive-label {
        display: inline-block;
        color: #72777c;
        background: #fdda58;
        border-radius: 4px;
        padding: 2px 4px;
        float: right;
        font-weight: 600;
        margin-top: -30px;
    }
</style>