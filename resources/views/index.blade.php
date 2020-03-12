<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <link href='https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Material+Icons' rel="stylesheet">
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>You Calendar App Title</title>
        <style> [v-cloak] { display: none; } </style>

        <link href="{{ url('css/app.css') }}" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" rel="stylesheet">
        {{-- <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"> --}}
        <link href='css/core/main.css' rel='stylesheet' />
        <link href='css/daygrid/main.css' rel='stylesheet' />
      </head>
      <body>
        <div id="app">
            <div class="container-fluid py-3">
                <div class="row">
                    <div class="col-md-4">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="base_month" class="mb-0">My Event</label>
                                <div class="input-group mb-3">
                                <input type="text" v-model="event_name">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="base_month" class="mb-0">From</label>
                                <div class="input-group mb-3">
                                  <vuejs-datepicker v-model="from_date"></vuejs-datepicker>
                                  <div class="input-group-append">
                                    <i class="fa fa-calendar input-group-text" aria-hidden="true"></i>
                                  </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="base_month" class="mb-0">To</label>
                                <div class="input-group mb-3">
                                <vuejs-datepicker v-model="to_date"></vuejs-datepicker>
                                <div class="input-group-append">
                                    <i class="fa fa-calendar input-group-text" aria-hidden="true"></i>
                                </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <input type="checkbox" v-model="days" value="Monday"><span class="mr-2"> Mon</span>
                                <input type="checkbox" v-model="days" value="Tuesday"><span class="mr-2"> Tue</span>
                                <input type="checkbox" v-model="days" value="Wednesday"><span class="mr-2"> Wed</span>
                                <input type="checkbox" v-model="days" value="Thursday"><span class="mr-2"> Thu</span>
                                <input type="checkbox" v-model="days" value="Friday"><span class="mr-2"> Fri</span>
                                <input type="checkbox" v-model="days" value="Saturday"><span class="mr-2"> Sat</span>
                                <input type="checkbox" v-model="days" value="Sunday"><span class="mr-2"> Sun</span>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col">
                                <button type="submit" v-on:click="submit" class="btn btn-primary rounded-0" :disabled="btn_state" >Save</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div id='calendar'></div>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        <script src="{{ url('js/app.js') }}"></script>
        <script src='js/core/main.js'></script>
        <script src='js/daygrid/main.js'></script>

<script src="https://unpkg.com/vue"></script>
<script src="https://unpkg.com/vuejs-datepicker"></script>
        <script>
            const app = new Vue({
              el: '#app',
              components: {
                  vuejsDatepicker
              },
              data() {
                  return {
                    days: [],
                    event_name: '',
                    from_date: '',
                    to_date: '',
                    btn_state: false,
                    events: [],
                    event_list: []
                  }
              },
            mounted() {
                this.fetchData();
              },
              methods: {
                  submit() {
                    if(this.event_name == '' || this.from_date == '' || this.to_date == '' || this.days.length == 0) {
                        alert('Fill all fields')
                        return;
                    }

                    // get all dates base on 2 dates
                    btn_state = true;
                    let start_date = moment(this.from_date).format()
                    let end_date = moment(this.to_date).format()
                    let curr_date = start_date

                    while(curr_date <= end_date) {
                        this.verifyDay(curr_date)
                        curr_date = moment(curr_date).add(1,'day').format()
                    }

                    let params = {
                        events : this.events
                    }
                    axios.post('/events', params)
                    .then((response) => {
                        toastr.success('Event successfuly saved.')
                        this.fetchData()
                    })

                  },
                  verifyDay(date) {
                    this.days.forEach((day) => {
                        if(moment(date).format('dddd') == day) {
                            this.events.push({
                                event_name : this.event_name,
                                event_date : moment(date).format('YYYY/MM/DD')
                            })
                        }
                    })
                  },
                  fetchData() {
                      axios.get('/events')
                      .then((response) => {
                        this.event_list = response.data
                        this.displayCalendar()
                      })
                  },
                  displayCalendar() {
                    var calendarEl = document.getElementById('calendar');
                    calendarEl.innerHTML = "";
                    var calendar = new FullCalendar.Calendar(calendarEl, {
                        plugins: [ 'dayGrid' ],
                        events: this.event_list,
                        header: {
                            left: 'prevYear,prev,next,nextYear today',
                            center: 'title',
                            right: 'dayGridMonth,dayGridWeek'
                        },
                    });
                    calendar.render();
                  }
              },
              watch: {
                  days() {
                      console.log(this.days)
                  }
              },
            })
            </script>
    </body>
</html>
