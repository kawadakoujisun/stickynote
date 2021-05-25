<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h1>Todoリスト</h1>
                <ul>
                <li v-for="todo in todos">{{ todo['name'] }}</li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data(){
            return {
                todos : []
            }
        },
        mounted(){
            axios.get('/api/todos').then(response => (this.todos = response.data));                                      
        },
    }
</script>
