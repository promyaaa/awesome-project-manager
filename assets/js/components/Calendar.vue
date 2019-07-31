<template>
    <div class="container">
        <project-nav v-on:get-project="setProject"></project-nav>
        <div class="lists border-for-nav">
            <div class="row">
                <div class="col-12">
                	<FullCalendar
					  defaultView="dayGridMonth"
					  :plugins="calendarPlugins"
					  :weekends="false"
					  :events="[
					    { title: 'event 1', date: '2019-08-01' },
					    { title: 'event 2', date: '2019-08-02' },
					    { title: 'event 3', date: '2019-07-05' },
					    { title: 'event 4', date: '2019-07-10' }
					  ]"
					  />
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import FullCalendar from '@fullcalendar/vue'
import dayGridPlugin from '@fullcalendar/daygrid'

export default {
  components: {
    FullCalendar // make the <FullCalendar> tag available
  },
  data() {
    return {
      calendarPlugins: [ dayGridPlugin ]
    }
  }
}

</script>

<style>

@import '~@fullcalendar/core/main.css';
@import '~@fullcalendar/daygrid/main.css';

</style>