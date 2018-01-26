<template>
    <input
        type="text"
        :value="formattedValue"
        :placeholder="placeholder"
        @input="updateValue($event.target.value)"
        class="form-control"
    >
</template>

<script>
    export default {
        props: {
            value: {
                type: String,
                required: true,
                default: ''
            },

            placeholder: {
                type: String,
                required: false,
                default: 'set due date'
            },

            changeMonthYear: {
                type: Boolean,
                required: false,
                default: false
            }
        },

        data() {
            return {
                formatMap: {
                    // Day
                    d: 'dd',
                    D: 'D',
                    j: 'd',
                    l: 'DD',

                    // Month
                    F: 'MM',
                    m: 'mm',
                    M: 'M',
                    n: 'm',

                    // Year
                    o: 'yy', // not exactly same. see php date doc for details
                    Y: 'yy',
                    y: 'y'
                }
            };
        },

        computed: {
            formattedValue() {
                return this.value;
            }
        },

        mounted() {
            const vm = this;

            jQuery(vm.$el).datetimepicker({
                dateFormat: "yy-mm-dd",
                changeMonth: vm.changeMonthYear,
                changeYear: vm.changeMonthYear,

                beforeShow() {
                    jQuery(this).datepicker('widget').addClass('fusion-pm-datepicker');
                },

                onSelect(date) {
                    vm.updateValue(date);
                }
            });
        },

        methods: {
            updateValue(value) {
                // if (!value) {
                //     value = moment().format('YYYY-MM-DD');
                // } else {
                //     value = moment(value, weMail.momentDateFormat).format('YYYY-MM-DD');
                // }
                this.$emit('input', value);
            }
        }
    };
</script>