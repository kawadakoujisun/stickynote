<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h1>Todoリスト</h1>
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
                newTodo : ''
            }
        },
        mounted(){
            axios.get('/api/todos').then(response => (this.todos = response.data));                                      
        
            window.Echo.channel('todo-added-channel')
                        .listen('TodoAdded',response => {
                            this.todos.push(response.todo);
                        });  
        },
        methods:{
            addTodo(){
                console.log(window.laravel.user);
                
                axios.post('/api/todos',{
                    name : this.newTodo
                })
                .then(response => this.todos.push(response.data));         
    
                this.newTodo = '';
    
            }
        }
    }
</script>
