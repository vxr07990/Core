package org.swissphpfriends.gradle.task

import org.gradle.api.tasks.TaskAction
import org.swissphpfriends.gradle.AbstractBaseTask

class PhpUnit extends AbstractBaseTask {

    String path = './'

    Boolean outputTap = false

    Boolean outputTestdox = false

    Boolean groupIncludeMode = false

    Boolean groupExcludeMode = false

    String groups = ''

    String bootstrapFile = ''

    String configurationFile = ''

    String coverageFormat = 'none'

    String coverageTarget = ''

    @TaskAction
    def phpunit() {

        this.checkPhpInstallation()

        ArrayList<String> command = this.buildCommand()

        this.printBuildMessage('running phpunit tests...')
        if(this.verbose) {
            println('RUNNING: ' + command.toString());
        }

        Process phpUnitProcess = new ProcessBuilder(command)
                .directory(new File(this.workingDirectory))
                .redirectErrorStream(true)
                .start()

        phpUnitProcess.inputStream.eachLine { println it }
        phpUnitProcess.waitFor()

        if (phpUnitProcess.exitValue()) {
            //throw new TaskExecutionException(this, new Throwable('command "php composer.phar install" failed!'))
            println "FAILED -.-"
        }
    }

    public ArrayList<String> buildCommand() {
        ArrayList<String> command = new ArrayList<String>();

        // executable
        command.add(this.executable);

        // verbosity
        if (this.verbose) {
            command.add('-v');
        };

        // groups
        if (this.groupIncludeMode) {
            command.add('--group');
            command.add(this.groups);
        } else if (this.groupExcludeMode) {
            command.add('--exclude-group');
            command.add(this.groups);
        }

        // output format
        if (this.outputTap) {
            command.add('--tap');
        }
        if (this.outputTestdox) {
            command.add('--testdox');
        }

        // boostrap file
        if (!this.bootstrapFile.isEmpty()) {
            File bootstrapFile = new File(this.bootstrapFile);

            command.add('--bootstrap');
            command.add(this.bootstrapFile.toString());
        }

        // config file
        if (!this.configurationFile.isEmpty()) {
            File configurationFile = new File(this.configurationFile);

            command.add('--configuration')
            command.add(this.configurationFile.toString());
        }

        //coverage
        if (!this.coverageFormat.equals('none')) {
            File coverageTarget = new File(this.coverageTarget);

            command.add('--coverage-' + this.coverageFormat)
            command.add(this.coverageTarget.toString());
        }

        // path
        command.add(this.path);

        return command;
    }
}