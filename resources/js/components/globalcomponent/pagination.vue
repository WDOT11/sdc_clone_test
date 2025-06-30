<template>
    <nav class="pagination is-centered is-rounded" role="navigation" aria-label="pagination">
        <div class="onewhitebg mt-2  flex items-center justify-between sm:px-1">
            <div class=" ">
                <div>
                    <p class="text-sm  themetextcolor">
                        Page <span class="font-medium">#{{pagination.current_page}}</span> Total Page <span class="font-medium">{{pagination.last_page}}</span>
                    </p>
                </div>
                <div class=" mt-2">
                    <!-- -space-x-px -->
                    <nav class="mainpagination" aria-label="Pagination">
                         <!-- First Page -->
                        <a href="javascript:void(0)" @click.prevent="changePage(1)" :disabled="pagination.current_page == 1" :class="pagination.current_page == 1 ? 'cursor-not-allowed opacity-50' : 'cursor-pointer'">
                            <svg id="fi_11502464" enable-background="new 0 0 512 512" height="14" viewBox="0 0 512 512" width="14" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path clip-rule="evenodd" d="m52.246 239.578c-9.043 9.043-9.043 23.803 0 32.846l177.132 177.126c9.057 9.057 23.79 9.057 32.846 0l9.908-9.908c9.065-9.065 9.067-23.79.001-32.857l-134.37-134.357c-9.067-9.065-9.067-23.793 0-32.858l134.369-134.358c9.067-9.066 9.066-23.79-.001-32.856l-9.908-9.908c-9.043-9.043-23.803-9.043-32.846 0zm364.742-177.132c9.052-9.053 23.799-9.052 32.851 0l9.908 9.908c9.048 9.048 9.049 23.808 0 32.857l-134.363 134.359c-9.066 9.066-9.066 23.792 0 32.858l134.364 134.358c9.052 9.051 9.051 23.806 0 32.857l-9.908 9.908c-9.056 9.056-23.795 9.057-32.851 0l-161.398-161.403c-45.274 45.229-33.822 33.759-.033-.033l-15.69-15.691c-9.049-9.049-9.048-23.798 0-32.847z" fill-rule="evenodd"></path></svg>
                        </a>
                        <a href="javascript.void(0)" @click.prevent="changePage(pagination.current_page < 2 ? pagination.current_page : pagination.current_page  - 1)" :disabled="pagination.current_page < 2" :class="(pagination.current_page < 2) ? 'cursor-not-allowed' : 'cursor-pointer' "  >
                            <span class="sr-only">Previous</span>
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                        </a>

                        <a v-for="page in pages" href="javascript.void(0)" :class="isCurrentPage(page) ? 'current' : ''" @click.prevent="changePage(page)" class="">
                            {{ page }}
                        </a>

                        <a href="javascript.void(0)" @click.prevent="changePage(pagination.current_page + 1)" :disabled="pagination.current_page >= pagination.last_page" :class="(pagination.current_page >= pagination.last_page) ? 'cursor-not-allowed' : 'cursor-pointer' " >
                            <span class="sr-only">Next</span>
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
                        </a>

                        <!-- Last Page -->
                        <a href="javascript:void(0)" @click.prevent="changePage(pagination.last_page)" :disabled="pagination.current_page == pagination.last_page" :class="pagination.current_page == pagination.last_page ? 'cursor-not-allowed opacity-50' : 'cursor-pointer'">
                            <svg id="fi_11502458" enable-background="new 0 0 512 512" height="14" viewBox="0 0 512 512" width="14" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path clip-rule="evenodd" d="m239.873 105.208 134.367 134.36c9.067 9.067 9.067 23.792 0 32.858l-134.369 134.36c-9.065 9.064-9.058 23.796.003 32.858l9.906 9.908c9.054 9.056 23.796 9.054 32.85-.001l177.128-177.128c9.047-9.047 9.044-23.801-.002-32.847l-177.126-177.133c-9.049-9.05-23.801-9.049-32.851 0l-9.908 9.908c-9.052 9.053-9.05 23.806.002 32.857zm-187.618 0c-9.064-9.064-9.061-23.794.001-32.858l9.906-9.908c9.049-9.05 23.801-9.049 32.85.001l177.126 177.134c9.061 9.062 9.063 23.784 0 32.847l-177.125 177.128c-9.055 9.055-23.795 9.057-32.85.001l-9.906-9.908c-9.062-9.063-9.066-23.794-.001-32.858l134.367-134.36c9.065-9.065 9.065-23.794 0-32.858z" fill-rule="evenodd"></path></svg>
                        </a>
                    </nav>
                </div>
            </div>
        </div>
    </nav>
</template>
<script>
    export default {
        props: ['pagination', 'offset', 'paginate'],
        methods: {
            isCurrentPage(page) {
                return this.pagination.current_page == page;
            },
            changePage(page) {
                if (page > this.pagination.last_page) {
                    page = this.pagination.last_page;
                }
                if(this.isCurrentPage(page)) {
                    return;
                }

                this.pagination.current_page = page;
                this.paginate(this.pagination.current_page);
            }
        },
        computed: {
            pages() {
                let pages = [];
                let from = this.pagination.current_page - Math.floor(this.offset / 2);

                /* Set default page to 1 */
                if (from < 1) {
                    from = 1;
                }

                /* set to */
                let to = from + this.offset - 1;
                if (to > this.pagination.last_page) {
                    to = this.pagination.last_page;
                }

                /* fix: sometimes page number reduce to 10: 7 8 9 10 but it should be 6 7 8 9 10 */
                if( this.pagination.last_page >= this.offset && ( this.offset - 1 ) > (parseInt(to) - parseInt(from)) ) {
                    from = to - (this.offset - 1);
                }

                /* Creating page number */
                while (from <= to) {
                    pages.push(from);
                    from++;
                }

                return pages;
            }
        }
    }
</script>