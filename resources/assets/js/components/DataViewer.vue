<template>
    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="panel-title">{{title}}</span>
            <div>
                <router-link :to="create" class="btn btn-primary btn-sm">Create</router-link>
                <button class="btn btn-default btn-sm" @click="showFilter = !showFilter">F</button>
            </div>
        </div>
        <div class="panel-body">
            <div class="filter" v-if="showFilter">
                <div class="filter-column">
                    <select class="form-control" v-model="params.search_column">
                        <option v-for="column in filter" :value="column">{{column}}</option>
                    </select>
                </div>
                <div class="filter-operator">
                    <select class="form-control" v-model="params.search_operator">
                        <option v-for="(value, key) in operators" :value="key">{{value}}</option>
                    </select>
                </div>
                <div class="filter-input">
                    <input type="text" class="form-control" v-model="params.search_query_1"
                        @keyup.enter="fetchData" placeholder="Search">
                </div>
                <div class="filter-input" v-if="params.search_operator === 'between'">
                    <input type="text" class="form-control" v-model="params.search_query_2"
                        @keyup.enter="fetchData" placeholder="Search">
                </div>
                <div class="filter-btn">
                    <button class="btn btn-primary btn-sm btn-block" @click="fetchData">Filter</button>
                </div>
            </div>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th v-for="item in thead">
                            <div class="dataviewer-th" @click="sort(item.key)" v-if="item.sort">
                                <span>{{item.title}}</span>
                                <span v-if="params.column === item.key">
                                    <span v-if="params.direction === 'asc'">&#x25B2;</span>
                                    <span v-else>&#x25BC;</span>
                                </span>
                            </div>
                            <div v-else>
                                <span>{{item.title}}</span>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <slot v-for="item in model.data" :item="item"></slot>
                </tbody>
            </table>
        </div>
        <div class="panel-footer pagination-footer">
            <div class="pagination-item">
                <span>Per page: </span>
                <select v-model="params.per_page" @change="fetchData">
                    <option>10</option>
                    <option>25</option>
                    <option>50</option>
                </select>
            </div>
            <div class="pagination-item">
                <small>Showing {{model.from}} - {{model.to}} of {{model.total}}</small>
            </div>
            <div class="pagination-item">
                <small>Current page: </small>
                <input type="text" class="pagination-input" v-model="params.page"
                    @keyup.enter="fetchData">
                <small> of {{model.last_page}}</small>
            </div>
            <div class="pagination-item">
                <button @click="prev" class="btn btn-default btn-sm">Prev</button>
                <button @click="next" class="btn btn-default btn-sm">Next</button>
            </div>
        </div>
    </div>
</template>
<script>
    import Vue from 'vue'
    import axios from 'axios'

    export default {
        props: ['source', 'thead', 'filter', 'create', 'title'],
        data() {
            return {
                showFilter: false,
                model: {
                    data: []
                },
                params: {
                    column: 'id',
                    direction: 'desc',
                    per_page: 10,
                    page: 1,
                    search_column: 'id',
                    search_operator: 'equal_to',
                    search_query_1: '',
                    search_query_2: ''
                },
                operators: {
                    equal_to: '=',
                    not_equal: '<>',
                    less_than: '<',
                    greater_than: '>',
                    less_than_or_equal_to: '<=',
                    greater_than_or_equal_to: '>=',
                    in: 'IN',
                    not_in: 'NOT IN',
                    like: 'LIKE',
                    between: 'BETWEEN'
                }
            }
        },
        beforeMount() {
            this.fetchData()
        },
        methods: {
            next() {
                if(this.model.next_page_url) {
                    this.params.page++
                    this.fetchData()
                }
            },
            prev() {
                if(this.model.prev_page_url) {
                    this.params.page--
                    this.fetchData()
                }
            },
            sort(column) {
                if(column === this.params.column) {
                    if(this.params.direction === 'desc') {
                        this.params.direction = 'asc'
                    } else {
                        this.params.direction = 'desc'
                    }
                } else {
                    this.params.column = column
                    this.params.direction = 'asc'
                }

                this.fetchData()
            },
            fetchData() {
                var vm = this
                axios.get(this.buildURL())
                    .then(function(response) {
                        Vue.set(vm.$data, 'model', response.data.model)
                    })
                    .catch(function(error) {
                        console.log(error)
                    })
            },
            buildURL() {
                var p = this.params
                return `${this.source}?column=${p.column}&direction=${p.direction}&per_page=${p.per_page}&page=${p.page}&search_column=${p.search_column}&search_operator=${p.search_operator}&search_query_1=${p.search_query_1}&search_query_2=${p.search_query_2}`
            }
        }
    }
</script>