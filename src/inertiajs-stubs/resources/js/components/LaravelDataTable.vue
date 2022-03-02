<template>
    <div class="laravel-datatable">
        <div class="mb-6 flex justify-between items-center">
            <slot name="search-before" :selectedRows="selectedRows"></slot>
            <v-text-field v-if="!hideSearche"
                v-model="search"
                append-icon="search"
                label="Rechercher"
                single-line
                hide-details
                clearable
                solo
                dense
            ></v-text-field>
            <slot name="search-after" :selectedRows="selectedRows">
                <inertia-link v-if="!hideCreate" as="v-btn" class="text-capitalize ml-3" color="primary" :href="createRoute || route('dashboard.'+datatable.inertiaView+'.create')">
                    <span>{{ createText || 'Create' }}</span> 
                    <!-- <span class="hidden md:inline"> {{ datatable.inertiaView }}</span> -->
                </inertia-link>
            </slot>
        </div>
        <slot name="before-table" :selectedRows="selectedRows"></slot>
        <v-data-table
            :headers="headers"
            :items="query.data"
            :server-items-length="query.total"
            :loading="loading"
            :options.sync="options"
            v-sortable-data-table
            :show-select="selectable"
            v-model="selectedRows"
            @sorted="saveOrder"
            hide-default-footer
            class="shadow"
        >
            <template #item="{item}">
                <tr :class="[item.cssClass, {'bg-yellow-100': isItemSelected(item)}]">
                    <td v-if="selectable">
                        <v-checkbox hide-details v-if="!item.notSelectable" color="primary" v-model="selectedRows" :value="item"></v-checkbox>
                    </td>
                    <td v-for="(col, index) in headers"
                        :key="index"
                    >
                        <v-runtime-template v-if="col.key != 'actions'"
                            :template="'<div>'+getColValue(item, col.key)+'</div>'">
                        </v-runtime-template>
                        <template v-else>
                            <template v-if="(datatable && datatable.isInertia || datatable && datatable.inertiaView) && !hideActions">
                                <v-tooltip left>
                                    <template v-slot:activator="{ on, attrs }">
                                        <inertia-link v-if="showAction('show')" as="v-btn" icon small color="default" :href="route('dashboard.'+datatable.inertiaView+'.show', item.id)">
                                            <v-icon v-bind="attrs" v-on="on">remove_red_eye</v-icon>
                                        </inertia-link>
                                    </template>
                                    <span>Show details</span>
                                </v-tooltip>
                                <v-tooltip left>
                                    <template v-slot:activator="{ on, attrs }">
                                        <inertia-link v-if="showAction('edit')" as="v-btn" icon small color="default" :href="route('dashboard.'+datatable.inertiaView+'.edit', item.id)">
                                            <v-icon v-bind="attrs" v-on="on">edit</v-icon>
                                        </inertia-link>
                                    </template>
                                    <span>Edit</span>
                                </v-tooltip>
                                <v-tooltip left>
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-btn v-if="showAction('delete')" icon small color="default" @click="deleteRow(item.id)">
                                            <v-icon v-bind="attrs" v-on="on">delete</v-icon>
                                        </v-btn>
                                    </template>
                                    <span>Delete</span>
                                </v-tooltip>
                            </template>
                            <slot name="extra-actions" :item="item"></slot>
                        </template>
                    </td>
                </tr>
            </template>
        </v-data-table>
        <div class="text-xs-center pt-2" v-if='query.last_page > 1'>
            <v-pagination @input="getDataFromApi" v-model="options.page" :length="query.last_page"></v-pagination>
        </div>
    </div>
</template>

<script>
import VRuntimeTemplate from "v-runtime-template";
import { Sortable } from 'sortablejs'
export default {
    props: [
        'data',
        'solo',
        'selectable',
        'sortable',
        'saveOrderUrl',
        'url',
        'createRoute',
        'createText',
        'hideSearche',
        'hideCreate',
        'hideActions',
        'actions',
        'searchTerm'
    ],
    components: {
        VRuntimeTemplate
    },
    data() {
        return {
            loading: false,
            search: this.data ? this.data.searchTerm : null,
            headers: [],
            message: 'Silver',
            filters: this.sortable,
            selectedRows: [],
            options: {},
            datatable: this.data,
            model: this.data ? this.data.model : null,
            query: this.data && this.data.query || {},
            ajaxHeader: [],
            clonedArray: this.data && this.data.query ? JSON.parse(JSON.stringify(this.data.query.data)) : []
        }
    },
    watch: {
        search: _.debounce(function(){
            this.getDataFromApi(this.options.page);
        }, 500),
        data: function(newValue) {
            this.initData(newValue)
        },
        options: {
            handler (newOptions, oldOptions) {
                if (oldOptions.sortBy) {
                    if (newOptions.sortBy[0] &&
                        (newOptions.sortBy[0] != oldOptions.sortBy[0]) ||
                        (newOptions.sortDesc[0] != oldOptions.sortDesc[0])
                    ) {
                        const sortParam = newOptions.sortDesc[0] == false ? `sort=${newOptions.sortBy[0]}` : `sort=-${newOptions.sortBy[0]}`
                        this.getDataFromApi(this.options.page, sortParam);
                    }
                }
            },
            deep: true
        }
    },
    mounted: async function() {
        if (this.datatable) {
            this.headers = this.createHeaders(this.datatable.headers)
        }
        if (this.url) {
            this.query = {}
            await this.getDataFromApi()
            this.headers = this.createHeaders(this.ajaxHeader)
        }
        this.$root.$on('datatable:refresh', async (params)=> {
            await this.getDataFromApi(this.options.page, params);
            this.$root.$emit('datatable:refresh:success')
        })
        Array.prototype.move = function (from, to) {
          this.splice(to, 0, this.splice(from, 1)[0]);
        }
    },
    destroyed() {
        this.$root.$off('datatable:refresh')
    },
    directives: {
        sortableDataTable: {
            bind: (el, binding, vnode) => {
                if (vnode.context.sortable) {
                    const options = {
                        animation: 150,
                        onUpdate: function (event) {
                            vnode.child.$emit('sorted', event)
                        }
                    }
                    Sortable.create(el.getElementsByTagName('tbody')[0], options)
                }
            }
        }
    },
    methods: {
        isItemSelected(item) {
            return this.selectedRows.findIndex(r => r.id === item.id) > -1
        },
        async deleteRow(id) {
            const {value} = await this.$swal({
                title: 'Are you sure you want to delete this',
                // text: this.confirmMessage,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                cancelButtonText: 'No'
            })
            if (value) {
                this.$inertia.delete(this.route('dashboard.'+this.datatable.inertiaView+'.destroy', id))
            }
        },
        createHeaders(headers) {
            let newHeaders = [];
            headers.map(col => {
                if (col.visible != false) {
                    if (this.hideActions && col.value == 'actions' || col == 'actions') {
                        return col
                    }else {
                        newHeaders.push({ 
                            text: this.__(col.text || col), 
                            value: col.sortValue || col.value || col,
                            key: col.value || col,
                            sortable: col.sortable == false ? false : true
                        })
                    }
                }
            });
            return newHeaders;
        },
        getColValue(item, key) {
            return _.get(item, key) || '---'
            // const keys = key.split('.')
            // for (let colKey of keys) { item = item[colKey] }
            // return item || '---'
        },
        showAction(action) {
            if (this.actions && this.actions.length) {
                return this.actions.indexOf(action) > -1
            }else if (this.actions && !this.actions.length) {
                return false
            }else {
                return true
            }
        },
        replaceParams(params) {
            const filterParams = this.getParams('http://test.com?'+this.filters)
            const filterKeys = Object.keys(filterParams)
            const _params = this.getParams('http://test.com?'+params)
            for(let paramKey in _params) {
                filterParams[paramKey] = _params[paramKey]
            }
            let query = ''
            for(let param in filterParams) {
                if (
                    String(filterParams[param]) != 'null' && 
                    String(filterParams[param]) != '-null' && 
                    String(filterParams[param]) != 'undefined' && 
                    String(filterParams[param]) != '-undefined'
                ) {
                    query += `${param}=${filterParams[param]}&`
                }
            }
            return query.substr(0, query.length-1)
        },
        initData(data) {
            this.datatable = data
            this.query = data.query || {};
            this.model = data.model
            this.ajaxHeader = data.headers
            this.clonedArray = JSON.parse(JSON.stringify(data.query.data))
            this.selectedRows = []
        },
        async getDataFromApi(page = null, params) {
            this.filters = this.replaceParams(params)
            const urlParams = this.getQuery() && !this.url ? this.getQuery() + (this.filters ? `&${this.filters}` : '') : (this.filters ? `${this.filters}` : '')
            this.loading = true;
            let queryParams = page ? `?page=${page}&${urlParams}` : `?${urlParams}`;
            queryParams += this.search ? `&search=${encodeURIComponent(this.search.toLowerCase())}` : '';
            let url = (this.url || this.query.path) + queryParams
            const firstOccuranceIndex = url.search(/\?/) + 1;
            url = url.substr(0, firstOccuranceIndex) + url.slice(firstOccuranceIndex).replace(/\?/g, '&');
            try {
                const {data} = await axios.get(`${url}&datatable=true`);
                this.loading = false;
                this.initData(data)
            }catch(e) {
                this.loading = false
            }
        },
        getQuery (url = window.location.href) {
            let params = {};
            let parser = document.createElement('a');
            parser.href = url;
            let query = parser.search.substring(1);
            return query;
        },
        getParams (url = window.location.href) {
            let params = {};
            let parser = document.createElement('a');
            parser.href = url;
            let query = parser.search.substring(1);
            let vars = query.split('&');
            for (let i = 0; i < vars.length; i++) {
                let pair = vars[i].split('=');
                if (String(pair[0]) != 'null' && String(pair[0]) != "undefined")
                    params[pair[0]] = decodeURIComponent(pair[1]);
            }
            return params;
        },
        saveOrder (event) {
            this.clonedArray.move(event.oldIndex, event.newIndex)
            if (this.model) {
                try {
                    axios.post(this.saveOrderUrl || '/dashboard/save-order', {
                        newOrder: this.clonedArray.map(f => f.id),
                        model: this.model
                    })
    
                }catch(e) {
                    console.error('Error when save new order', e)
                    this.$swal({icon: 'error', title: `Error when save new order`, text: e.response ? e.response.data.message : 'Internal server error'})
                }
            }
        }
    }
}
</script>

<style lang="scss" scoped>
.laravel-datatable {
    .v-input--checkbox {
        margin: 0 !important;
        .v-input__slot {
            width: fit-content !important;
        }
    }
}
</style>