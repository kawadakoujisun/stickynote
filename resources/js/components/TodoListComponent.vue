<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h1>Todoリスト</h1>
                <p>name = {{ user.name }}, id = {{ user.id }}</p>
                <ul>
                <li v-for="todo in todos">{{ todo['name'] }}</li>
                </ul>
                <input type="text" v-model="newTodo" @blur="addTodo">
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data(){
            return {
                todos : [],
                newTodo : '',
                user : {
                    name : window.laravel.user['name'],
                    id   : window.laravel.user['id'],
                },
            };
        },
        mounted(){
            // axios.get('/api/todos').then(response => (this.todos = response.data));                                      
            // axios.get(window.laravel.asset + '/api/todos').then(response => {
            axios.get(window.laravel.asset + '/api/todos', {
                params: {
                    user_id : window.laravel.user['id'],
                },
            })
            .then(response => {
                /*
                // paramsでユーザーIDを渡しマッチしたTodoしか返さないようにしたので、ここではユーザーIDのチェックはしなくてよい。
                this.todos = [];
                response.data.forEach(elem => {
                    if (elem['user_id'] == window.laravel.user['id']) {
                        this.todos.push(elem);
                    }
                });
                */
                
                this.todos = response.data;
            });
            
            window.Echo.private('todo-added-channel.' + window.laravel.user['id'])
                        .listen('TodoAdded',response => {
                            this.todos.push(response.todo);
                        });  
        },
        methods:{
            addTodo(){
                axios.post(window.laravel.asset + '/api/todos', {
                    name : this.newTodo,
                    user_id : window.laravel.user['id']
                })
                .then(response => this.todos.push(response.data));         
    
                this.newTodo = '';
    
            }
        }
    }
</script>
