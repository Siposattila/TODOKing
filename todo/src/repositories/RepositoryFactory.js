import TodoRepository from "./TodoRepository";

const repositories = {
	todos: TodoRepository
}

export const RepositoryFactory = {
	get: name => repositories[name]
}
