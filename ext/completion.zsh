_altax_command_list () {
  altax --no-ansi | sed "1,/Available commands/d" | awk '/^  [a-z]+/ { print $1 }'
}

_altax() {

  typeset -A opt_args
  _arguments \
    '1: :->first'
  case $state in
    first)
      compadd `_altax_command_list`
      ;;
  esac

}
compdef _altax altax
