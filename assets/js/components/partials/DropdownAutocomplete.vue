<template>
    <div style="position:relative" v-bind:class="{'open':openSuggestion}">
        <input class="form-control" 
        type="text"
        placeholder="enter minimum 3 char of assignee name ..."
        v-model="queryuser"
        @input="updateValue($event.target.value)"
        @keydown.enter = 'enter'
        @keydown.down = 'down'
        @keydown.up = 'up'
        @keydown.esc = 'close'
        >
        
        <ul class="dropdown-menu" style="width:100%; min-height:100px">
            <li v-for="(suggestion, index) in matches"
                v-bind:class="{'active': isActive(index)}"
                @click="suggestionClick(index)">
                <a>
                    <img :src="suggestion.data.avatar_url" 
                        class="dropdown-user-img"
                        width="20" height="20">
                    <span class="inline-block">
                        <small>{{ suggestion.data.display_name }}</small>
                    </span>
                </a>
            </li>
            <li v-html="searchIndicator"></li>
        </ul>
</div>
</template>

<script>
    export default {

        props: {
            currentselect: {
                type: String,
            },
        },
        watch: {
            currentselect(newval, oldval) {
                if ( ! newval ) {
                    this.queryuser = ''
                }
            },
            queryuser: function () {
                this.searchQueryIsDirty = true
                if(this.queryuser.length > 2) {
                    this.expensiveOperation()
                }
                if ( !this.queryuser ) {
                    this.$emit('userselect', {})
                    return
                }
            }
        },
        data () {
            return {
                open: false,
                current: 0,
                queryuser: '',
                suggestions: [],
                searchQueryIsDirty: false,
                isCalculating: false
            }
        },

        computed: {
            // Filtering the suggestion based on the input
            matches () {
                var vm = this;
                return vm.suggestions.filter((obj) => {
                    return obj.data.display_name.toLowerCase().indexOf(vm.queryuser.toLowerCase()) >= 0
                })
            },

            openSuggestion () {
                // return this.queryuser !== '' && this.matches.length !== 0 && this.open === true;
                return this.queryuser !== '' && this.open === true;
            },

            minCharCount() {
                return Math.max(0, 3-this.queryuser.length);
            },

            resultCount() {
                return this.suggestions.length > 0 ? this.suggestions.length : 'no';
            },

            searchIndicator: function () {
                var output;
                if (this.isCalculating) {
                    return '<span style="margin-left: 15px"><i class="fa fa-refresh fa-spin fa-fw" aria-hidden="true"></i><small><i> fetching...</i></small></span>';
                } else if (this.searchQueryIsDirty) {
                    if(this.queryuser.length < 3) {
                        output = '<span style="margin-left: 15px"><small><i>typing...(' + this.minCharCount + ' more char needed)</i></small></span>'
                    } else {
                        output = '<span style="margin-left: 15px"><small><i>typing...</i></small></span>'
                    }
                    return output;
                } else {
                    return '<span style="margin-left: 15px"><small><i>'+ this.resultCount +' result found</i></small></span>'
                }
            }
        },

        methods: {
            expensiveOperation: _.debounce(function () {
                this.isCalculating = true
                setTimeout(function () {
                    this.isCalculating = false
                    this.searchQueryIsDirty = false
                    this.fetchSuggestion();
                }.bind(this), 500)
            }, 500),

            fetchSuggestion() {
                var vm = this,
                    i,
                    data = {
                        action : 'fpm-get-search-users',
                        nonce : fpm.nonce,
                        project_id: vm.$route.params.projectid,
                        user_types: ['administrator', 'fpm_member'],
                        query_string: vm.queryuser
                    };
                vm.suggestions = [];

                if(!data.query_string)
                    return
                
                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    if ( resp.success ) {
                        for( i = 0; i < resp.data.length; i++ ) {
                            vm.suggestions.push(resp.data[i]);
                        }
                    }
                });
            },
            updateValue (value) {
                if (this.open === false) {
                    this.open = true
                    this.current = 0
                }
            },

            // When enter pressed on the input
            enter () {
                if(this.matches.length < 1){
                    return;
                }
                this.$emit('userselect', this.matches[this.current].data)
                this.queryuser = this.matches[this.current].data.display_name
                this.open = false
            },

            // When up pressed while suggestions are open
            up () {
                if (this.current > 0) {
                    this.current--
                }
            },

            // When up pressed while suggestions are open
            down () {
                if (this.current < this.matches.length - 1) {
                    this.current++
                }
            },

            close () {
                this.open = false;
            },

            // For highlighting element
            isActive (index) {
                return index === this.current
            },

            // When one of the suggestion is clicked
            suggestionClick (index) {
                if(this.matches.length < 1) {
                    return;
                }
                this.$emit('userselect', this.matches[index].data)
                this.queryuser = this.matches[index].data.display_name
                this.open = false
            }
        },

        created() {
            this.queryuser = this.currentselect ? this.currentselect : ''
        }
    }
</script>

<style>
    .dropdown-user-img {
        display: inline-block;
        vertical-align: top;
        border-radius: 45px;
        margin-right: 5px;
    }
</style>