import Vue from './vue';
import router from './routes';
import store from './store';

window.wpmedia = wp.media;

// Tooltip directive
Vue.directive('tooltip', {
    bind: function( el, binding, vnode ) {
        jQuery(el).tooltip('show');
    },
    unbind: function( el, binding, vnode ) {
        jQuery(el).tooltip('destroy');
    }
});


new Vue({

    el: '#app',
    router: router,
    components: {

    },
    data: {
        store: store
    },

    compluted: {

    },

    methods: {
        getLocalizeString: function() {
            var vm = this,
                todo,
                data = {
                    action : 'fpm-get-local-data'
                };

            jQuery.post( fpm.ajaxurl, data, function( resp ) {

                if ( resp.success ) {
                    vm.store.setLocalizeData(resp.data);
                } else {

                }
            });
        },
    },

    created: function() {
        // this.getLocalizeString();
        // jQuery(document).on('heartbeat-send', function(e, data) {
        //     console.log('test heartbeat-send');
        // });
    }
});

// Vue.directive('focus', {
//     inserted: function (el) {
//     // Focus the element
//         el.focus()
//     }
// });

// EXAMPLE DIRECTIVE
// <template>
//    <input type="text" v-focus>
// </template>

// <script>
//  const focus = {
//     inserted(el) {
//       el.focus()
//     },
//   }

//   export default {
//     directives: { focus },
//     // ...
//   }
// </script>