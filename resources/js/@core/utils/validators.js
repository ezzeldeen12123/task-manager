import { isEmpty, isEmptyArray, isNullOrUndefined } from './index'


// 👉 Required Validator
export const requiredValidator = value => {
  if (isNullOrUndefined(value) || isEmptyArray(value) || value === false)
    return 'This field is required'
  
  return !!String(value).trim().length || 'This field is required'
}

// 👉 Email Validator
export const emailValidator = value => {
  if (isEmpty(value))
    return true
  const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
  if (Array.isArray(value))
    return value.every(val => re.test(String(val))) || 'email'
  
  return re.test(String(value)) || 'email'
}

// 👉 Password Validator
export const passwordValidator = password => {
  const regExp = /(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%&*()]).{8,}/
  const validPassword = regExp.test(password)
  
  return (validPassword || 'password')
}

export const passwordValidatorIf = (password, noCheck = false) => {
  if(noCheck == true){
    return true;
  }
  const regExp = /(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%&*()]).{8,}/
  const validPassword = regExp.test(password)
  
  return (validPassword || 'password')
}

// 👉 Confirm Password Validator
export const confirmedValidator = (value, target) => value === target || 'passwordConfirm'

// 👉 Length Validator
export const lengthValidator = (value, length) => {
  if (isEmpty(value))
    return true

    return String(value).length >= length || 'minCharacter', { length: length }
}
